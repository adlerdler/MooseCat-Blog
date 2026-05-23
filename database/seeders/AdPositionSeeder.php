<?php

namespace Database\Seeders;

use App\Models\AdPosition;
use Illuminate\Database\Seeder;

class AdPositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'name' => '首页顶部横幅',
                'label_key' => 'ad_positions.home_top_banner',
                'description' => '首页顶部的大型横幅广告位',
                'default_width' => 1200,
                'default_height' => 300,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '侧边栏广告',
                'label_key' => 'ad_positions.sidebar_ad',
                'description' => '文章列表页和详情页的侧边栏广告位',
                'default_width' => 300,
                'default_height' => 250,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '文章内嵌广告',
                'label_key' => 'ad_positions.article_inline_ad',
                'description' => '文章内容中间插入的广告位',
                'default_width' => 800,
                'default_height' => 150,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => '页脚广告',
                'label_key' => 'ad_positions.footer_ad',
                'description' => '网站页脚的通栏广告位',
                'default_width' => 1200,
                'default_height' => 90,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => '移动端底部广告',
                'label_key' => 'ad_positions.mobile_bottom_ad',
                'description' => '移动端页面底部的固定广告位',
                'default_width' => 375,
                'default_height' => 60,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($positions as $position) {
            AdPosition::create($position);
        }
    }
}
