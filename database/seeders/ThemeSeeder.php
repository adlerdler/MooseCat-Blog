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
                'name' => 'default',
                'label' => '默认主题',
                'color' => '#3b82f6',
                'sort_order' => 1,
                'is_active' => true,
                'is_default' => true,
                'preview_image' => '/images/themes/default.png',
            ],
            [
                'name' => 'minimal',
                'label' => '简约主题',
                'color' => '#000000',
                'sort_order' => 2,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/minimal.png',
            ],
            [
                'name' => 'dark',
                'label' => '暗黑主题',
                'color' => '#8b5cf6',
                'sort_order' => 3,
                'is_active' => true,
                'is_default' => false,
                'preview_image' => '/images/themes/dark.png',
            ],
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
