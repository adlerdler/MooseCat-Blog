<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'code'        => 'en',
                'name'        => 'English',
                'native_name' => 'English',
                'flag'        => '🇺🇸',
                'file_path'   => '/locales/en.json',
                'direction'   => 'ltr',
                'is_default'  => true,
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'code'        => 'zh',
                'name'        => 'Chinese (Simplified)',
                'native_name' => '简体中文',
                'flag'        => '🇨🇳',
                'file_path'   => '/locales/zh.json',
                'direction'   => 'ltr',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'code'        => 'zh-TW',
                'name'        => 'Chinese (Traditional)',
                'native_name' => '繁體中文',
                'flag'        => '🇹🇼',
                'file_path'   => '/locales/zh-TW.json',
                'direction'   => 'ltr',
                'is_default'  => false,
                'is_active'   => true,
                'sort_order'  => 3,
            ],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(
                ['code' => $lang['code']],
                $lang
            );
        }
    }
}
