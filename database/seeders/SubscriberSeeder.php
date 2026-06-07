<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        Subscriber::updateOrCreate(
            ['email' => 'adler@example.com'],
            [
                'name' => 'Adler',
                'source' => 'website',
                'is_active' => true,
                'subscribed_at' => now(),
            ]
        );
    }
}
