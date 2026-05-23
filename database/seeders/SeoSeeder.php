<?php

namespace Database\Seeders;

use App\Models\Seo;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $seos = [
            [
                'key' => 'home',
                'value' => json_encode([
                    'title' => 'Archyx - 技术与创意的个人博客平台',
                    'description' => '分享编程技术、设计创意和生活感悟的个人博客',
                    'keywords' => '博客,技术,编程,设计,创意,生活',
                    'og_image' => '/images/og-home.jpg',
                ]),
            ],
            [
                'key' => 'posts',
                'value' => json_encode([
                    'title' => '文章列表 - Archyx',
                    'description' => '浏览最新的技术文章和教程',
                    'keywords' => '文章,教程,技术博客',
                    'og_image' => '/images/og-posts.jpg',
                ]),
            ],
            [
                'key' => 'projects',
                'value' => json_encode([
                    'title' => '项目展示 - Archyx',
                    'description' => '查看我的开源项目和技术作品',
                    'keywords' => '项目,开源,作品',
                    'og_image' => '/images/og-projects.jpg',
                ]),
            ],
        ];

        foreach ($seos as $seo) {
            Seo::create($seo);
        }
    }
}
