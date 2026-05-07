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
                'description' => 'A deep dive into why less is more.',
                'video_id' => 'dQw4w9WgXcQ',
                'platform' => 'youtube',
            ],
            [
                'title' => 'Laravel 11 Features',
                'description' => 'Quick overview of what is new in Laravel 11.',
                'video_id' => 'BV6nO5K0vE8',
                'platform' => 'bilibili',
            ]
        ];

        foreach ($videos as $v) {
            Video::create($v);
        }
    }
}
