<?php

namespace Database\Seeders;

use App\Models\Visit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        $visits = [
            [
                'visitable_id' => 1,
                'visitable_type' => 'App\\Models\\Post',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'referrer' => 'https://google.com',
            ],
            [
                'visitable_id' => 2,
                'visitable_type' => 'App\\Models\\Post',
                'ip_address' => '192.168.1.2',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                'referrer' => 'https://baidu.com',
            ],
            [
                'visitable_id' => 1,
                'visitable_type' => 'App\\Models\\Project',
                'ip_address' => '192.168.1.3',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'referrer' => 'https://github.com',
            ],
            [
                'visitable_id' => 1,
                'visitable_type' => 'App\\Models\\Video',
                'ip_address' => '192.168.1.4',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 13)',
                'referrer' => 'https://twitter.com',
            ],
            [
                'visitable_id' => 3,
                'visitable_type' => 'App\\Models\\Post',
                'ip_address' => '192.168.1.5',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0)',
                'referrer' => 'https://bing.com',
            ],
        ];

        foreach ($visits as $visit) {
            Visit::create($visit);
        }
    }
}
