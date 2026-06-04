<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserLevel;

/**
 * UserLevelService - 用户等级服务
 *
 * 提供等级查询、升级检测等辅助方法。
 */
class UserLevelService
{
    /**
     * 获取用户当前等级
     */
    public function getCurrentLevel(User $user): ?UserLevel
    {
        return $user->userLevel;
    }

    /**
     * 获取用户应达到的等级（根据当前积分）
     */
    public function getTargetLevel(User $user): ?UserLevel
    {
        return UserLevel::getLevelForPoints((int) $user->points);
    }

    /**
     * 获取默认新手等级（min_points = 0 的第一个活跃等级）
     */
    public function getDefaultLevel(): ?UserLevel
    {
        return UserLevel::where('is_active', true)
            ->where('min_points', 0)
            ->orderBy('sort_order')
            ->first();
    }

    /**
     * 同步用户等级：如果当前等级不匹配则升级
     *
     * @return ?UserLevel 如果升级了返回新等级，否则 null
     */
    public function syncLevel(User $user): ?UserLevel
    {
        $targetLevel = UserLevel::getLevelForPoints((int) $user->points);

        if ($targetLevel && $targetLevel->id !== $user->level_id) {
            $user->update(['level_id' => $targetLevel->id]);
            return $targetLevel;
        }

        return null;
    }
}
