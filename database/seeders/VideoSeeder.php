<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            [
                'title' => 'Minimalist Design Principles',
                'slug' => 'minimalist-design-principles',
                'description' => 'A deep dive into why less is more.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_id' => 'dQw4w9WgXcQ',
                'cover_image' => '/images/videos/minimalist-design-cover.jpg',
                'status' => 'published',
                'category_id' => null,
                'platform' => 'youtube',
                'thumbnail' => '/images/videos/minimalist-design.jpg',
                'duration' => '15:30',
                'views_count' => 12500,
                'likes_count' => 890,
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Laravel 11 Features',
                'slug' => 'laravel-11-features',
                'description' => 'Quick overview of what is new in Laravel 11.',
                'video_url' => 'https://www.bilibili.com/video/BV6nO5K0vE8',
                'video_id' => 'BV6nO5K0vE8',
                'cover_image' => '/images/videos/laravel-11-cover.jpg',
                'status' => 'published',
                'category_id' => null,
                'platform' => 'bilibili',
                'thumbnail' => '/images/videos/laravel-11.jpg',
                'duration' => '10:45',
                'views_count' => 8900,
                'likes_count' => 560,
                'published_at' => now()->subDays(15),
            ]
        ];

        foreach ($videos as $v) {
            Video::create($v);
        }
    }
}
