<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\PointsService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class UpdateLastLoginAt
{
    public function __construct(
        protected PointsService $pointsService,
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // ─── 每日登录积分 +5（仅当天首次登录）────
        if (! $this->pointsService->hasDailyLoginPoints($user)) {
            $this->pointsService->awardPoints(
                user: $user,
                points: 5,
                type: 'login',
                description: '每日登录奖励',
            );
        }

        // ─── 更新最后登录时间 ────
        DB::table('users')
            ->where('id', $user->id)
            ->update(['last_login_at' => now()]);
    }
}
