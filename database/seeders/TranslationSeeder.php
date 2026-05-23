<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $translations = [
            ['group' => 'navigation', 'key' => 'home', 'text' => json_encode(['zh-CN' => '首页', 'en' => 'Home']), 'description' => '导航菜单 - 首页', 'is_active' => true, 'sort_order' => 1],
            ['group' => 'navigation', 'key' => 'posts', 'text' => json_encode(['zh-CN' => '文章', 'en' => 'Posts']), 'description' => '导航菜单 - 文章', 'is_active' => true, 'sort_order' => 2],
            ['group' => 'navigation', 'key' => 'projects', 'text' => json_encode(['zh-CN' => '项目', 'en' => 'Projects']), 'description' => '导航菜单 - 项目', 'is_active' => true, 'sort_order' => 3],
            ['group' => 'navigation', 'key' => 'resources', 'text' => json_encode(['zh-CN' => '资源', 'en' => 'Resources']), 'description' => '导航菜单 - 资源', 'is_active' => true, 'sort_order' => 4],
            ['group' => 'navigation', 'key' => 'videos', 'text' => json_encode(['zh-CN' => '视频', 'en' => 'Videos']), 'description' => '导航菜单 - 视频', 'is_active' => true, 'sort_order' => 5],
            ['group' => 'navigation', 'key' => 'about', 'text' => json_encode(['zh-CN' => '关于', 'en' => 'About']), 'description' => '导航菜单 - 关于', 'is_active' => true, 'sort_order' => 6],
            ['group' => 'common', 'key' => 'read_more', 'text' => json_encode(['zh-CN' => '阅读更多', 'en' => 'Read More']), 'description' => '通用 - 阅读更多', 'is_active' => true, 'sort_order' => 1],
            ['group' => 'common', 'key' => 'search', 'text' => json_encode(['zh-CN' => '搜索', 'en' => 'Search']), 'description' => '通用 - 搜索', 'is_active' => true, 'sort_order' => 2],
            ['group' => 'common', 'key' => 'submit', 'text' => json_encode(['zh-CN' => '提交', 'en' => 'Submit']), 'description' => '通用 - 提交', 'is_active' => true, 'sort_order' => 3],
            ['group' => 'common', 'key' => 'cancel', 'text' => json_encode(['zh-CN' => '取消', 'en' => 'Cancel']), 'description' => '通用 - 取消', 'is_active' => true, 'sort_order' => 4],
            ['group' => 'auth', 'key' => 'login', 'text' => json_encode(['zh-CN' => '登录', 'en' => 'Login']), 'description' => '认证 - 登录', 'is_active' => true, 'sort_order' => 1],
            ['group' => 'auth', 'key' => 'register', 'text' => json_encode(['zh-CN' => '注册', 'en' => 'Register']), 'description' => '认证 - 注册', 'is_active' => true, 'sort_order' => 2],
            ['group' => 'auth', 'key' => 'logout', 'text' => json_encode(['zh-CN' => '退出登录', 'en' => 'Logout']), 'description' => '认证 - 退出登录', 'is_active' => true, 'sort_order' => 3],
            ['group' => 'auth', 'key' => 'email', 'text' => json_encode(['zh-CN' => '邮箱', 'en' => 'Email']), 'description' => '认证 - 邮箱', 'is_active' => true, 'sort_order' => 4],
            ['group' => 'auth', 'key' => 'password', 'text' => json_encode(['zh-CN' => '密码', 'en' => 'Password']), 'description' => '认证 - 密码', 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($translations as $translation) {
            Translation::create($translation);
        }
    }
}
