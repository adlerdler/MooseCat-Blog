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
                'preview_image' => '/images/themes/default.png',
                'is_active' => true,
                'config' => json_encode([
                    'primary_color' => '#3b82f6',
                    'secondary_color' => '#10b981',
                    'font_family' => 'Inter, sans-serif',
                    'layout' => 'sidebar',
                    'dark_mode' => true,
                ]),
            ],
            [
                'name' => 'minimal',
                'label' => '简约主题',
                'preview_image' => '/images/themes/minimal.png',
                'is_active' => false,
                'config' => json_encode([
                    'primary_color' => '#000000',
                    'secondary_color' => '#6b7280',
                    'font_family' => 'system-ui, sans-serif',
                    'layout' => 'full-width',
                    'dark_mode' => false,
                ]),
            ],
            [
                'name' => 'dark',
                'label' => '暗黑主题',
                'preview_image' => '/images/themes/dark.png',
                'is_active' => false,
                'config' => json_encode([
                    'primary_color' => '#8b5cf6',
                    'secondary_color' => '#ec4899',
                    'font_family' => 'Inter, sans-serif',
                    'layout' => 'sidebar',
                    'dark_mode' => true,
                ]),
            ],
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
