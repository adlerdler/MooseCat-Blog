<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@archyx.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Archyx_admin123'),
                'level_id' => 1,
                'status' => 'active',
                'points' => 1000,
                'notifications' => true,
                'comment_approval_alert' => true,
                'new_user_alert' => true,
                'weekly_report' => true,
                'digest_email' => true,
                'digest_frequency' => 'weekly',
                'last_login_at' => now(),
            ]
        );
        $admin->assignRole('Administrator');

        $editor = User::firstOrCreate(
            ['email' => 'editor@archyx.com'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('password'),
                'level_id' => 2,
                'status' => 'active',
                'points' => 500,
                'notifications' => true,
                'comment_approval_alert' => true,
                'new_user_alert' => false,
                'weekly_report' => false,
                'digest_email' => false,
                'digest_frequency' => 'weekly',
                'last_login_at' => now(),
            ]
        );
        $editor->assignRole('Editor');

        $author = User::firstOrCreate(
            ['email' => 'author@archyx.com'],
            [
                'name' => 'Test Author',
                'password' => Hash::make('password'),
                'level_id' => 3,
                'status' => 'active',
                'points' => 300,
                'notifications' => true,
                'comment_approval_alert' => false,
                'new_user_alert' => false,
                'weekly_report' => false,
                'digest_email' => false,
                'digest_frequency' => 'weekly',
                'last_login_at' => now(),
            ]
        );
        $author->assignRole('Author');

        $user = User::firstOrCreate(
            ['email' => 'user@archyx.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'level_id' => 4,
                'status' => 'active',
                'points' => 100,
                'notifications' => false,
                'comment_approval_alert' => false,
                'new_user_alert' => false,
                'weekly_report' => false,
                'digest_email' => false,
                'digest_frequency' => 'weekly',
                'last_login_at' => now(),
            ]
        );
        $user->assignRole('Subscriber');
    }
}
