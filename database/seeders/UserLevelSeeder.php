<?php

namespace Database\Seeders;

use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => '新手', 'min_points' => 0, 'max_points' => 99, 'icon' => '🌱', 'color' => '#10b981', 'description' => '刚加入的新用户'],
            ['name' => '进阶', 'min_points' => 100, 'max_points' => 499, 'icon' => '🌿', 'color' => '#3b82f6', 'description' => '活跃的用户'],
            ['name' => '专家', 'min_points' => 500, 'max_points' => 999, 'icon' => '🌳', 'color' => '#8b5cf6', 'description' => '经验丰富的用户'],
            ['name' => '大师', 'min_points' => 1000, 'max_points' => 2999, 'icon' => '⭐', 'color' => '#f59e0b', 'description' => '社区的核心用户'],
            ['name' => '传奇', 'min_points' => 3000, 'max_points' => null, 'icon' => '👑', 'color' => '#ef4444', 'description' => '最高级别的用户'],
        ];

        foreach ($levels as $level) {
            UserLevel::create($level);
        }
    }
}
