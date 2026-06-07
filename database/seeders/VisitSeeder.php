<?php

namespace Database\Seeders;

use App\Models\Visit;
use App\Models\Post;
use App\Models\Project;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        // 动态获取资源（通过 slug 而非硬编码 ID）
        $post1 = Post::where('slug', 'the-geometry-of-perception')->first();
        $post2 = Post::where('slug', 'typography-as-architecture')->first();
        $post3 = Post::where('slug', 'manifesto-of-the-machine')->first();
        $project1 = Project::where('slug', 'archyx-blog-system')->first();
        $video1 = Video::first();

        $visits = [];

        if ($post1) {
            $visits[] = [
                'visitable_id' => $post1->id,
                'visitable_type' => Post::class,
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'referrer' => 'https://google.com',
            ];
        }

        if ($post2) {
            $visits[] = [
                'visitable_id' => $post2->id,
                'visitable_type' => Post::class,
                'ip_address' => '192.168.1.2',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                'referrer' => 'https://baidu.com',
            ];
        }

        if ($project1) {
            $visits[] = [
                'visitable_id' => $project1->id,
                'visitable_type' => Project::class,
                'ip_address' => '192.168.1.3',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'referrer' => 'https://github.com',
            ];
        }

        if ($video1) {
            $visits[] = [
                'visitable_id' => $video1->id,
                'visitable_type' => Video::class,
                'ip_address' => '192.168.1.4',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 13)',
                'referrer' => 'https://twitter.com',
            ];
        }

        if ($post3) {
            $visits[] = [
                'visitable_id' => $post3->id,
                'visitable_type' => Post::class,
                'ip_address' => '192.168.1.5',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0)',
                'referrer' => 'https://bing.com',
            ];
        }

        foreach ($visits as $visit) {
            Visit::updateOrCreate(
                [
                    'visitable_id' => $visit['visitable_id'],
                    'visitable_type' => $visit['visitable_type'],
                    'ip_address' => $visit['ip_address']
                ],
                $visit
            );
        }
    }
}
