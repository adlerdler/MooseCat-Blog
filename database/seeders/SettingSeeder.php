<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                // 基本信息
                'name' => 'ARCHYX',
                'description' => 'Constructivist Digital Archive',
                'site_url' => 'https://archyx.com',
                'copyright' => '© 2024 ARCHYX. All rights reserved.',
                'logo' => '/images/logo.png',
                'favicon' => '/images/favicon.ico',
                'timezone' => 'Asia/Shanghai',
                
                // 功能开关
                'maintenance' => false,
                'author_bio' => false,
                'comments' => true,
                'registration' => true,
                'comment_approval' => false,
                'newsletter' => true,
                'social_login' => false,
                'search' => true,
                
                // 性能优化
                'cache' => true,
                'cache_duration' => 3600,
                'minification' => true,
                'lazy_load' => true,
                'cdn' => false,
                'cdn_url' => '',
                
                // 文件上传
                'max_upload_size' => 10,
                'file_types' => json_encode(['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx']),
            ]
        );
    }
}
