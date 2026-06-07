<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use App\Models\AdPosition;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        // 动态获取广告位置（通过 name 而非硬编码 ID）
        $headerPosition = AdPosition::where('name', 'header')->first();
        $sidebarPosition = AdPosition::where('name', 'sidebar')->first();
        $betweenPostsPosition = AdPosition::where('name', 'between_posts')->first();
        $inContentPosition = AdPosition::where('name', 'in_content')->first();
        $footerPosition = AdPosition::where('name', 'footer')->first();
        $popupPosition = AdPosition::where('name', 'popup')->first();
        $videoBottomPosition = AdPosition::where('name', 'video_bottom')->first();

        $advertisements = [
            [
                'title' => 'Minimalist Hosting',
                'image_url' => 'https://via.placeholder.com/1200x300',
                'link_url' => 'https://example.com/hosting',
                'position' => $headerPosition,
                'is_active' => true,
                'clicks_count' => 1250,
                'views_count' => 15800,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(30),
            ],
            [
                'title' => 'Cloud Server Pro',
                'image_url' => 'https://via.placeholder.com/1200x300',
                'link_url' => 'https://example.com/cloud',
                'position' => $headerPosition,
                'is_active' => true,
                'clicks_count' => 890,
                'views_count' => 12300,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addDays(45),
            ],
            [
                'title' => 'Developer Tools Bundle',
                'image_url' => 'https://via.placeholder.com/300x250',
                'link_url' => 'https://example.com/tools',
                'position' => $sidebarPosition,
                'is_active' => true,
                'clicks_count' => 560,
                'views_count' => 8900,
                'start_date' => now()->subDays(20),
                'end_date' => now()->addDays(60),
            ],
            [
                'title' => 'Learn Laravel 11',
                'image_url' => 'https://via.placeholder.com/300x250',
                'link_url' => 'https://example.com/laravel-course',
                'position' => $sidebarPosition,
                'is_active' => true,
                'clicks_count' => 2100,
                'views_count' => 25600,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(90),
            ],
            [
                'title' => 'Vue.js Mastery',
                'image_url' => 'https://via.placeholder.com/300x250',
                'link_url' => 'https://example.com/vue-course',
                'position' => $sidebarPosition,
                'is_active' => true,
                'clicks_count' => 1800,
                'views_count' => 22400,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(85),
            ],
            [
                'title' => 'Premium UI Kit',
                'image_url' => 'https://via.placeholder.com/728x90',
                'link_url' => 'https://example.com/ui-kit',
                'position' => $betweenPostsPosition,
                'is_active' => true,
                'clicks_count' => 430,
                'views_count' => 6700,
                'start_date' => now()->subDays(25),
                'end_date' => now()->addDays(35),
            ],
            [
                'title' => 'API Development Guide',
                'image_url' => 'https://via.placeholder.com/728x90',
                'link_url' => 'https://example.com/api-guide',
                'position' => $betweenPostsPosition,
                'is_active' => true,
                'clicks_count' => 670,
                'views_count' => 9200,
                'start_date' => now()->subDays(12),
                'end_date' => now()->addDays(48),
            ],
            [
                'title' => 'Database Optimization',
                'image_url' => 'https://via.placeholder.com/300x600',
                'link_url' => 'https://example.com/db-optimization',
                'position' => $inContentPosition,
                'is_active' => true,
                'clicks_count' => 320,
                'views_count' => 5100,
                'start_date' => now()->subDays(18),
                'end_date' => now()->addDays(42),
            ],
            [
                'title' => 'Security Best Practices',
                'image_url' => 'https://via.placeholder.com/300x600',
                'link_url' => 'https://example.com/security',
                'position' => $inContentPosition,
                'is_active' => false,
                'clicks_count' => 150,
                'views_count' => 2800,
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(10),
            ],
            [
                'title' => 'Mobile App Development',
                'image_url' => 'https://via.placeholder.com/320x50',
                'link_url' => 'https://example.com/mobile-dev',
                'position' => $footerPosition,
                'is_active' => true,
                'clicks_count' => 980,
                'views_count' => 14500,
                'start_date' => now()->subDays(8),
                'end_date' => now()->addDays(52),
            ],
            [
                'title' => 'DevOps Essentials',
                'image_url' => 'https://via.placeholder.com/320x50',
                'link_url' => 'https://example.com/devops',
                'position' => $footerPosition,
                'is_active' => true,
                'clicks_count' => 740,
                'views_count' => 11200,
                'start_date' => now()->subDays(22),
                'end_date' => now()->addDays(38),
            ],
            [
                'title' => 'JavaScript Frameworks',
                'image_url' => 'https://via.placeholder.com/320x50',
                'link_url' => 'https://example.com/js-frameworks',
                'position' => $footerPosition,
                'is_active' => true,
                'clicks_count' => 1560,
                'views_count' => 19800,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(97),
            ],
        ];

        foreach ($advertisements as $ad) {
            $position = $ad['position'];
            if (!$position) {
                continue;
            }

            Advertisement::updateOrCreate(
                ['title' => $ad['title']],
                [
                    'image_url' => $ad['image_url'],
                    'link_url' => $ad['link_url'],
                    'position_id' => $position->id,
                    'is_active' => $ad['is_active'],
                    'clicks_count' => $ad['clicks_count'],
                    'views_count' => $ad['views_count'],
                    'start_date' => $ad['start_date'],
                    'end_date' => $ad['end_date'],
                ]
            );
        }
    }
}
