<?php

namespace Database\Seeders;

use App\Models\Video;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::role('Administrator')->first();

        $videos = [
            [
                'title' => 'Minimalist Design Principles',
                'slug' => Str::slug('Minimalist Design Principles'),
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
                'meta_title' => 'Minimalist Design Principles',
                'meta_description' => 'A deep dive into why less is more.',
                'meta_keywords' => 'design,minimalist,ui,ux',
                'author_id' => $author->id,
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Laravel 11 Features',
                'slug' => Str::slug('Laravel 11 Features'),
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
                'meta_title' => 'Laravel 11 Features',
                'meta_description' => 'Quick overview of what is new in Laravel 11.',
                'meta_keywords' => 'laravel,php,framework',
                'author_id' => $author->id,
                'published_at' => now()->subDays(15),
            ]
        ];

        foreach ($videos as $v) {
            Video::firstOrCreate(['slug' => $v['slug']], $v);
        }
    }
}
