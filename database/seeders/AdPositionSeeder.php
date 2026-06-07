<?php

namespace Database\Seeders;

use App\Models\AdPosition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdPositionSeeder extends Seeder
{
    public function run(): void
    {
        // 移除 truncate 避免破坏外键约束和已有关联

        $positions = [
            [
                'name' => 'header',
                'label_key' => 'ad_positions.home_top_banner',
                'description' => 'Header banner ad slot below navigation',
                'default_width' => 1200,
                'default_height' => 300,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'sidebar',
                'label_key' => 'ad_positions.sidebar_ad',
                'description' => 'Sidebar ad slot on post/project detail pages',
                'default_width' => 300,
                'default_height' => 250,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'between_posts',
                'label_key' => 'ad_positions.between_posts_ad',
                'description' => 'Injected between every N posts in list pages',
                'default_width' => 800,
                'default_height' => 150,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'in_content',
                'label_key' => 'ad_positions.in_content_ad',
                'description' => 'In-article inline ad slot',
                'default_width' => 800,
                'default_height' => 150,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'footer',
                'label_key' => 'ad_positions.footer_ad',
                'description' => 'Footer banner ad slot above site footer',
                'default_width' => 1200,
                'default_height' => 90,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'popup',
                'label_key' => 'ad_positions.popup_ad',
                'description' => 'Popup/modal ad shown on first visit',
                'default_width' => 400,
                'default_height' => 500,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'video_bottom',
                'label_key' => 'ad_positions.video_bottom_ad',
                'description' => 'Ad slot below video player on video detail pages',
                'default_width' => 1200,
                'default_height' => 200,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($positions as $position) {
            AdPosition::updateOrCreate(
                ['name' => $position['name']],
                $position
            );
        }
    }
}
