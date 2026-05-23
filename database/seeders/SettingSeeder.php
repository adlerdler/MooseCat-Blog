<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => json_encode('Archyx'), 'type' => 'string', 'group' => 'basic', 'label' => '网站名称', 'description' => '网站名称', 'is_public' => true],
            ['key' => 'site_description', 'value' => json_encode('一个专注于技术与创意的个人博客平台'), 'type' => 'string', 'group' => 'basic', 'label' => '网站描述', 'description' => '网站描述', 'is_public' => true],
            ['key' => 'site_keywords', 'value' => json_encode('博客,技术,创意,编程,设计'), 'type' => 'string', 'group' => 'basic', 'label' => '网站关键词', 'description' => '网站关键词', 'is_public' => true],
            ['key' => 'site_logo', 'value' => json_encode('/images/logo.png'), 'type' => 'string', 'group' => 'basic', 'label' => '网站 Logo', 'description' => '网站 Logo', 'is_public' => true],
            ['key' => 'site_favicon', 'value' => json_encode('/images/favicon.ico'), 'type' => 'string', 'group' => 'basic', 'label' => '网站 Favicon', 'description' => '网站 Favicon', 'is_public' => true],
            ['key' => 'posts_per_page', 'value' => json_encode(10), 'type' => 'number', 'group' => 'content', 'label' => '每页文章数', 'description' => '每页显示文章数', 'is_public' => true],
            ['key' => 'enable_comments', 'value' => json_encode(true), 'type' => 'boolean', 'group' => 'content', 'label' => '开启评论', 'description' => '是否开启评论', 'is_public' => true],
            ['key' => 'comment_approval', 'value' => json_encode(true), 'type' => 'boolean', 'group' => 'content', 'label' => '评论审核', 'description' => '评论是否需要审核', 'is_public' => false],
            ['key' => 'enable_registration', 'value' => json_encode(true), 'type' => 'boolean', 'group' => 'user', 'label' => '开放注册', 'description' => '是否开放注册', 'is_public' => true],
            ['key' => 'default_role', 'value' => json_encode('subscriber'), 'type' => 'string', 'group' => 'user', 'label' => '默认角色', 'description' => '新用户默认角色', 'is_public' => false],
            ['key' => 'points_per_post', 'value' => json_encode(10), 'type' => 'number', 'group' => 'points', 'label' => '文章积分', 'description' => '发布文章获得积分', 'is_public' => true],
            ['key' => 'points_per_comment', 'value' => json_encode(2), 'type' => 'number', 'group' => 'points', 'label' => '评论积分', 'description' => '发表评论获得积分', 'is_public' => true],
            ['key' => 'points_per_login', 'value' => json_encode(1), 'type' => 'number', 'group' => 'points', 'label' => '登录积分', 'description' => '每日登录获得积分', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
