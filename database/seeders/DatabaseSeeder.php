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
        // 1. 创建用户
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@archyx.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Test User',
            'email' => 'user@archyx.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 2. 按顺序调用子 Seeder
        $this->call([
            // 基础数据
            RoleSeeder::class,
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
