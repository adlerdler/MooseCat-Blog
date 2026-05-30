<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $socialLinks = [
            [
                'type' => 'social_link',
                'platform' => 'github',
                'icon_name' => 'github',
                'label' => 'GITHUB',
                'url' => 'https://github.com',
                'icon' => 'github',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'type' => 'social_link',
                'platform' => 'twitter',
                'icon_name' => 'twitter',
                'label' => 'TWITTER',
                'url' => 'https://twitter.com',
                'icon' => 'twitter',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'type' => 'social_link',
                'platform' => 'linkedin',
                'icon_name' => 'linkedin',
                'label' => 'LINKEDIN',
                'url' => 'https://linkedin.com',
                'icon' => 'linkedin',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'type' => 'social_link',
                'platform' => 'youtube',
                'icon_name' => 'youtube',
                'label' => 'YOUTUBE',
                'url' => 'https://youtube.com',
                'icon' => 'youtube',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'type' => 'social_link',
                'platform' => 'weixin',
                'icon_name' => 'weixin',
                'label' => 'WEIXIN',
                'url' => 'https://weixin.qq.com',
                'icon' => 'weixin',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($socialLinks as $link) {
            FooterLink::create($link);
        }
    }
}
