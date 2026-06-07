<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@arkhyx.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Arkhyx_admin123'),
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
        $admin->syncRoles(['Administrator']);

        $editor = User::updateOrCreate(
            ['email' => 'editor@arkhyx.com'],
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
        $editor->syncRoles(['Editor']);

        $author = User::updateOrCreate(
            ['email' => 'author@arkhyx.com'],
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
        $author->syncRoles(['Author']);

        $user = User::updateOrCreate(
            ['email' => 'user@arkhyx.com'],
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
        $user->syncRoles(['Subscriber']);
    }
}
