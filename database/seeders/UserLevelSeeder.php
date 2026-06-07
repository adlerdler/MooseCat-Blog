<?php

namespace Database\Seeders;

use App\Models\UserLevel;
use Illuminate\Database\Seeder;

class UserLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => '新手', 'level' => 1, 'min_points' => 0, 'max_points' => 99, 'discount' => 0, 'color' => '#10b981', 'icon' => '🌱', 'description' => '刚加入的新用户', 'benefits' => json_encode(['基础浏览权限']), 'is_active' => true, 'sort_order' => 1],
            ['name' => '进阶', 'level' => 2, 'min_points' => 100, 'max_points' => 499, 'discount' => 5, 'color' => '#3b82f6', 'icon' => '🌿', 'description' => '活跃的用户', 'benefits' => json_encode(['基础浏览权限', '评论权限']), 'is_active' => true, 'sort_order' => 2],
            ['name' => '专家', 'level' => 3, 'min_points' => 500, 'max_points' => 999, 'discount' => 10, 'color' => '#8b5cf6', 'icon' => '🌳', 'description' => '经验丰富的用户', 'benefits' => json_encode(['基础浏览权限', '评论权限', '发布文章']), 'is_active' => true, 'sort_order' => 3],
            ['name' => '大师', 'level' => 4, 'min_points' => 1000, 'max_points' => 2999, 'discount' => 15, 'color' => '#f59e0b', 'icon' => '⭐', 'description' => '社区的核心用户', 'benefits' => json_encode(['基础浏览权限', '评论权限', '发布文章', '专属徽章']), 'is_active' => true, 'sort_order' => 4],
            ['name' => '传奇', 'level' => 5, 'min_points' => 3000, 'max_points' => null, 'discount' => 20, 'color' => '#ef4444', 'icon' => '👑', 'description' => '最高级别的用户', 'benefits' => json_encode(['全部权限', '专属客服', '定制徽章']), 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($levels as $level) {
            UserLevel::updateOrCreate(
                ['level' => $level['level']],
                $level
            );
        }
    }
}
