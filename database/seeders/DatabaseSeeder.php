<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 清除 spatie/laravel-permission 的缓存
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. 先运行角色和权限 Seeder
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2. 先创建用户等级数据（UserSeeder 依赖 level_id 外键）
        $this->call([
            UserLevelSeeder::class,
        ]);

        // 3. 创建用户并分配角色 (已移至 UserSeeder 统一管理)
        $this->call(UserSeeder::class);

        // 4. 按顺序调用子 Seeder
        $this->call([
            // 基础数据
            AdPositionSeeder::class,
            SettingSeeder::class,
            MenuSeeder::class,
            LanguageSeeder::class,
            SocialLinkSeeder::class,
            FooterLinkSeeder::class,
            ThemeSeeder::class,
            SeoSeeder::class,
            PageSeoSeeder::class,
            TranslationSeeder::class,
            
            // 用户相关
            AuthorProfileSeeder::class,
            VisitSeeder::class,
            
            // 原有数据
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            JournalSeeder::class,
            ProjectSeeder::class,
            VideoSeeder::class,
            ResourceSeeder::class,
            CommentSeeder::class,
            InteractionSeeder::class,
            AdvertisementSeeder::class,
            SubscriberSeeder::class,
        ]);

        // 4. 为种子用户分配默认新手等级（UserLevelSeeder 已创建等级数据）
        $defaultLevel = UserLevel::where('is_active', true)
            ->where('min_points', 0)
            ->orderBy('sort_order')
            ->first();
        if ($defaultLevel) {
            User::whereNull('level_id')->update(['level_id' => $defaultLevel->id]);
        }
    }
}
