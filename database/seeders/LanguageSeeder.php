<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'zh-CN', 'name' => '简体中文', 'native_name' => '简体中文', 'flag' => '🇨🇳', 'file_path' => 'lang/zh-CN.json', 'direction' => 'ltr', 'is_active' => true, 'is_default' => true, 'sort_order' => 1],
            ['code' => 'zh-TW', 'name' => '繁體中文', 'native_name' => '繁體中文', 'flag' => '🇹🇼', 'file_path' => 'lang/zh-TW.json', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 2],
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧', 'file_path' => 'lang/en.json', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 3],
            ['code' => 'ja', 'name' => '日本語', 'native_name' => '日本語', 'flag' => '🇯🇵', 'file_path' => 'lang/ja.json', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 4],
            ['code' => 'ko', 'name' => '한국어', 'native_name' => '한국어', 'flag' => '🇰🇷', 'file_path' => 'lang/ko.json', 'direction' => 'ltr', 'is_active' => false, 'is_default' => false, 'sort_order' => 5],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
