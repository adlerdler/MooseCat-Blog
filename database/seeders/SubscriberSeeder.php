<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        Subscriber::create([
            'email' => 'adler@example.com',
            'is_active' => true,
        ]);
    }
}
