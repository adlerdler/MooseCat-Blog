<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * PointsEarned - 积分获得事件
 *
 * 当用户获得积分时触发，用于扩展积分后的附加逻辑。
 */
class PointsEarned
{
    use Dispatchable;

    public function __construct(
        public readonly User $user,
        public readonly int $points,
        public readonly string $type,
        public readonly string $description,
        public readonly int $totalPoints,
    ) {}
}
