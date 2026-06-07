<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'construct-red',
                'label' => '建筑红',
                'color' => '#CF202E',
                'sort_order' => 1,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/construct-red.png',
            ],
            [
                'name' => 'ocean-blue',
                'label' => '海洋蓝',
                'color' => '#0066FF',
                'sort_order' => 2,
                'is_active' => true,
                'is_default' => true,
                'preview_image' => '/images/themes/ocean-blue.png',
            ],
            [
                'name' => 'forest-green',
                'label' => '森林绿',
                'color' => '#228B22',
                'sort_order' => 3,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/forest-green.png',
            ],
            [
                'name' => 'sunset-orange',
                'label' => '日落橙',
                'color' => '#FF8C00',
                'sort_order' => 4,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/sunset-orange.png',
            ],
            [
                'name' => 'purple-haze',
                'label' => '紫雾',
                'color' => '#8B5CF6',
                'sort_order' => 5,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/purple-haze.png',
            ],
            [
                'name' => 'pink-05',
                'label' => '粉色',
                'color' => '#FF007A',
                'sort_order' => 6,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/pink-05.png',
            ],
        ];

        foreach ($themes as $theme) {
            Theme::firstOrCreate(['name' => $theme['name']], $theme);
        }
    }
}
