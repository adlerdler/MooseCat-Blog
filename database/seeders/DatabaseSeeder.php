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
        // 1. 先运行角色和权限 Seeder
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2. 创建用户并分配角色
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@archyx.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'level_id' => 1,
                'status' => 'active',
                'points' => 1000,
            ]
        );
        $adminUser->assignRole('Administrator');

        $editorUser = User::firstOrCreate(
            ['email' => 'editor@archyx.com'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('password'),
                'level_id' => 2,
                'status' => 'active',
                'points' => 500,
            ]
        );
        $editorUser->assignRole('Editor');

        $authorUser = User::firstOrCreate(
            ['email' => 'author@archyx.com'],
            [
                'name' => 'Test Author',
                'password' => Hash::make('password'),
                'level_id' => 3,
                'status' => 'active',
                'points' => 300,
            ]
        );
        $authorUser->assignRole('Author');

        $testUser = User::firstOrCreate(
            ['email' => 'user@archyx.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'level_id' => 4,
                'status' => 'active',
                'points' => 100,
            ]
        );
        $testUser->assignRole('Subscriber');

        // 3. 按顺序调用子 Seeder
        $this->call([
            // 基础数据
            AdPositionSeeder::class,
            SettingSeeder::class,
            MenuSeeder::class,
            UserLevelSeeder::class,
            LanguageSeeder::class,
            SocialLinkSeeder::class,
            FooterLinkSeeder::class,
            ThemeSeeder::class,
            SeoSeeder::class,
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
