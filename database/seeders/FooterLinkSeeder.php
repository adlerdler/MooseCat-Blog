<?php

namespace Database\Seeders;

use App\Models\FooterLink;
use Illuminate\Database\Seeder;

class FooterLinkSeeder extends Seeder
{
    public function run(): void
    {
        $footerLinks = [
            ['type' => 'nav_link', 'platform' => 'categories', 'icon_name' => null, 'label' => '技术分享', 'url' => '/blog/tech', 'icon' => null, 'sort_order' => 1, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'categories', 'icon_name' => null, 'label' => '生活随笔', 'url' => '/blog/life', 'icon' => null, 'sort_order' => 2, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'categories', 'icon_name' => null, 'label' => '项目展示', 'url' => '/projects', 'icon' => null, 'sort_order' => 3, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '关于我们', 'url' => '/about', 'icon' => null, 'sort_order' => 1, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '联系方式', 'url' => '/contact', 'icon' => null, 'sort_order' => 2, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '隐私政策', 'url' => '/privacy', 'icon' => null, 'sort_order' => 3, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '服务条款', 'url' => '/terms', 'icon' => null, 'sort_order' => 4, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '免责声明', 'url' => '/disclaimer', 'icon' => null, 'sort_order' => 5, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '友情链接', 'url' => '/links', 'icon' => null, 'sort_order' => 6, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => null, 'label' => '网站地图', 'url' => '/sitemap.xml', 'icon' => null, 'sort_order' => 7, 'is_active' => true],
            ['type' => 'nav_link', 'platform' => 'data', 'icon_name' => 'rss', 'label' => 'RSS 订阅', 'url' => '/feed', 'icon' => 'rss', 'sort_order' => 8, 'is_active' => true],
        ];

        foreach ($footerLinks as $link) {
            FooterLink::updateOrCreate(
                ['label' => $link['label'], 'type' => 'nav_link'],
                $link
            );
        }
    }
}
