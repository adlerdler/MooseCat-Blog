<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\PointsEarned;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\UserPointsHistory;
use App\Notifications\LevelUpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PointsService - 积分系统核心服务
 *
 * 负责积分增减、历史记录写入、等级自动升级。
 */
class PointsService
{
    public function __construct(
        protected MailService $mailService,
    ) {}

    /**
     * 奖励积分（带等级升级检测）
     *
     * @return array{points: int, leveled_up: bool, new_level: ?UserLevel}
     */
    public function awardPoints(
        User $user,
        int $points,
        string $type,
        string $description,
        ?int $referenceId = null,
        ?string $referenceType = null,
    ): array {
        $leveledUp = false;
        $newLevel = null;

        DB::transaction(function () use ($user, $points, $type, $description, $referenceId, $referenceType, &$leveledUp, &$newLevel) {
            // 1. 增加积分
            $user->increment('points', $points);

            // 2. 写入历史记录
            UserPointsHistory::create([
                'user_id'        => $user->id,
                'points'         => $points,
                'type'           => $type,
                'description'    => $description,
                'reference_id'   => $referenceId,
                'reference_type' => $referenceType,
            ]);

            // 3. 触发 PointsEarned 事件（供后续扩展）
            $totalPoints = (int) $user->fresh()->points;
            event(new PointsEarned($user, $points, $type, $description, $totalPoints));

            // 4. 检测等级升级（仅更新数据库，通知和邮件在事务外发送）
            $oldLevelId = $user->level_id;
            $newLevel = UserLevel::getLevelForPoints($totalPoints);

            if ($newLevel && $newLevel->id !== $oldLevelId) {
                $user->update(['level_id' => $newLevel->id]);
                $leveledUp = true;
            }
        });

        // 5. 事务提交后，发送通知和邮件（失败不回滚积分）
        if ($leveledUp && $newLevel) {
            $totalPoints = (int) $user->points;
            $user->notify(new LevelUpNotification($newLevel, $totalPoints));
            $this->sendLevelUpEmail($user, $newLevel, $totalPoints);

            return [
                'points'      => $totalPoints,
                'leveled_up'  => true,
                'new_level'   => $newLevel,
            ];
        }

        return [
            'points'     => (int) $user->points,
            'leveled_up' => false,
            'new_level'  => null,
        ];
    }

    /**
     * 扣除积分（用于惩罚或消费）
     */
    public function deductPoints(
        User $user,
        int $points,
        string $type,
        string $description,
        ?int $referenceId = null,
        ?string $referenceType = null,
    ): void {
        DB::transaction(function () use ($user, $points, $type, $description, $referenceId, $referenceType) {
            $user->decrement('points', max(0, $points));

            UserPointsHistory::create([
                'user_id'        => $user->id,
                'points'         => -$points,
                'type'           => $type,
                'description'    => $description,
                'reference_id'   => $referenceId,
                'reference_type' => $referenceType,
            ]);
        });
    }

    /**
     * 检测用户今天是否已获得登录积分
     */
    public function hasDailyLoginPoints(User $user): bool
    {
        return UserPointsHistory::where('user_id', $user->id)
            ->where('type', 'login')
            ->whereDate('created_at', today())
            ->exists();
    }

    // ─────────────────────────────────────────────────────
    //  等级升级邮件发送
    // ─────────────────────────────────────────────────────

    /**
     * 发送等级升级邮件
     *
     * 在 DB 事务外调用，避免邮件发送失败回滚积分变更。
     * 邮件发送失败仅记录日志，不影响等级升级流程。
     */
    protected function sendLevelUpEmail(User $user, UserLevel $newLevel, int $totalPoints): void
    {
        // 检查用户是否开启了通知
        if (! $user->notifications) {
            return;
        }

        try {
            $brandName = $this->getBrandName();
            $nextLevel = UserLevel::where('min_points', '>', $newLevel->min_points)
                ->where('is_active', true)
                ->orderBy('min_points')
                ->first();

            $benefits = $newLevel->benefits ?? [];
            if (is_string($benefits)) {
                $benefits = json_decode($benefits, true) ?? [];
            }

            $subject = "🎉 You've reached {$newLevel->name}! | {$brandName}";
            $html = view('emails.level-up', [
                'brandName'        => $brandName,
                'userName'         => $user->name,
                'levelName'        => $newLevel->name,
                'levelColor'       => $newLevel->color ?? '#EF4444',
                'levelIcon'        => $newLevel->icon ?? '⭐',
                'levelDescription' => $newLevel->description ?? '',
                'totalPoints'      => number_format($totalPoints),
                'benefits'         => $benefits,
                'nextLevelName'    => $nextLevel?->name,
                'pointsToNext'     => $nextLevel ? $nextLevel->min_points - $totalPoints : null,
                'siteUrl'          => url('/'),
                'timestamp'        => now()->format('Y-m-d H:i'),
            ])->render();

            dispatch(new SendEmailJob($user->email, $subject, $html))->afterResponse();

            Log::info('Level up email sent', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'level'   => $newLevel->name,
                'points'  => $totalPoints,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send level up email', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'level'   => $newLevel->name,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * 获取站点品牌名称
     */
    protected function getBrandName(): string
    {
        $settingName = \App\Models\Setting::value('name');
        return $settingName ?: config('app.name', 'ARCHYX');
    }
}
