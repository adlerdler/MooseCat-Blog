<?php

namespace Database\Seeders;

use App\Models\Seo;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        Seo::updateOrCreate(
            ['id' => 1],
            [
                // 全局设置
                'meta_title' => 'Arkhyx - 技术与创意的个人博客平台',
                'meta_description' => '分享编程技术、设计创意和生活感悟的个人博客',
                'meta_keywords' => '博客,技术,编程,设计,创意,生活',
                
                // 统计代码
                'google_analytics' => '',
                'baidu_analytics' => '',
                
                // 功能开关
                'sitemap' => true,
                'robots' => true,
                'llm_txt' => false,
                
                // Open Graph 设置
                'canonical_url' => 'https://arkhyx.com',
                'og_image' => '/images/og-home.jpg',
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
            ]
        );
    }
}
