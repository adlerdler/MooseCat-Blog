<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $socialLinks = [
            [
                'platform' => 'GitHub',
                'url' => 'https://github.com',
                'icon' => 'github',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'platform' => 'Twitter',
                'url' => 'https://twitter.com',
                'icon' => 'twitter',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'platform' => 'LinkedIn',
                'url' => 'https://linkedin.com',
                'icon' => 'linkedin',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'platform' => 'YouTube',
                'url' => 'https://youtube.com',
                'icon' => 'youtube',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'platform' => '微信',
                'url' => 'https://weixin.qq.com',
                'icon' => 'wechat',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($socialLinks as $link) {
            SocialLink::create($link);
        }
    }
}
