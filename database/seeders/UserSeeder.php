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
            ]
        );
        $admin->assignRole('Administrator');

        $editor = User::firstOrCreate(
            ['email' => 'editor@archyx.com'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('password'),
                'level_id' => 2,
            ]
        );
        $editor->assignRole('Editor');

        $author = User::firstOrCreate(
            ['email' => 'author@archyx.com'],
            [
                'name' => 'Test Author',
                'password' => Hash::make('password'),
                'level_id' => 3,
            ]
        );
        $author->assignRole('Author');

        $user = User::firstOrCreate(
            ['email' => 'user@archyx.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'level_id' => 4,
            ]
        );
        $user->assignRole('Subscriber');
    }
}
