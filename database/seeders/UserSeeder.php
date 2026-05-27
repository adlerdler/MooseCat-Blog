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
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        $admin->assignRole('Administrator');

        $editor = User::firstOrCreate(
            ['email' => 'editor@archyx.com'],
            ['name' => 'Content Editor', 'password' => Hash::make('password')]
        );
        $editor->assignRole('Editor');

        $author = User::firstOrCreate(
            ['email' => 'author@archyx.com'],
            ['name' => 'Test Author', 'password' => Hash::make('password')]
        );
        $author->assignRole('Author');

        $user = User::firstOrCreate(
            ['email' => 'user@archyx.com'],
            ['name' => 'Test User', 'password' => Hash::make('password')]
        );
        $user->assignRole('Subscriber');
    }
}
