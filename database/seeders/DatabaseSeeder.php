<?php

namespace Database\Seeders;

use App\Models\User;
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
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@archyx.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('Administrator');

        $editorUser = User::create([
            'name' => 'Content Editor',
            'email' => 'editor@archyx.com',
            'password' => Hash::make('password'),
        ]);
        $editorUser->assignRole('Editor');

        $authorUser = User::create([
            'name' => 'Test Author',
            'email' => 'author@archyx.com',
            'password' => Hash::make('password'),
        ]);
        $authorUser->assignRole('Author');

        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'user@archyx.com',
            'password' => Hash::make('password'),
        ]);
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
    }
}
