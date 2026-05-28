<?php

namespace Database\Seeders;

use App\Models\PageSeo;
use Illuminate\Database\Seeder;

class PageSeoSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'page_key' => 'home',
                'title' => 'Archyx - 技术与创意的个人博客平台',
                'description' => '分享编程技术、设计创意和生活感悟的个人博客',
                'keywords' => '博客,技术,编程,设计,创意,生活',
                'og_image' => '/images/og-home.jpg',
            ],
            [
                'page_key' => 'blog',
                'title' => '博客文章 - Archyx',
                'description' => '浏览最新的技术文章、编程教程和开发经验分享',
                'keywords' => '技术文章,编程教程,开发经验,Laravel,Vue,React',
                'og_image' => '/images/og-blog.jpg',
            ],
            [
                'page_key' => 'projects',
                'title' => '项目展示 - Archyx',
                'description' => '查看我参与的开源项目和技术作品展示',
                'keywords' => '开源项目,技术作品,GitHub,个人项目',
                'og_image' => '/images/og-projects.jpg',
            ],
            [
                'page_key' => 'videos',
                'title' => '视频教程 - Archyx',
                'description' => '观看编程教学视频和技术分享',
                'keywords' => '视频教程,编程教学,技术分享',
                'og_image' => '/images/og-videos.jpg',
            ],
            [
                'page_key' => 'resources',
                'title' => '资源下载 - Archyx',
                'description' => '免费下载编程工具、模板和学习资源',
                'keywords' => '资源下载,编程工具,模板,学习资料',
                'og_image' => '/images/og-resources.jpg',
            ],
        ];

        foreach ($pages as $page) {
            PageSeo::create($page);
        }
    }
}
