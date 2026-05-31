<?php

namespace Database\Seeders;

use App\Models\AuthorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuthorProfileSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            AuthorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => $user->name,
                    'bio' => '这是一段个人简介，分享技术与生活的点滴。',
                    'avatar' => '/images/avatars/default.png',
                    'role_label' => '作者',
                    'role_title' => '技术博主',
                    'status_label' => 'active',
                    'status_text' => '活跃',
                    'is_active' => true,
                    'social_links' => json_encode([
                        'github' => 'https://github.com',
                        'twitter' => 'https://twitter.com',
                        'linkedin' => 'https://linkedin.com',
                    ]),
                    'expertise' => json_encode(['PHP', 'Laravel', 'Vue.js']),
                    'skills' => json_encode([
                        ['id' => 1, 'label' => '后端开发', 'value' => 95, 'description' => '精通 Laravel、PHP', 'category' => 'backend', 'sort_order' => 1],
                        ['id' => 2, 'label' => '前端开发', 'value' => 92, 'description' => 'Vue.js、React', 'category' => 'frontend', 'sort_order' => 2],
                        ['id' => 3, 'label' => '数据库设计', 'value' => 84, 'description' => 'MySQL、PostgreSQL', 'category' => 'database', 'sort_order' => 3],
                    ]),
                    'manifestos' => json_encode([
                        ['title' => 'manifesto', 'content' => '我探索建筑与技术的交叉领域，构建连接物理与虚拟世界的数字系统。', 'sort_order' => 1],
                        ['title' => 'manifesto_highlight', 'content' => '每一行代码都是一块砖。每个系统都是一个结构。设计既是蓝图也是基础。', 'sort_order' => 2],
                        ['title' => 'manifesto_conclusion', 'content' => '通过计算思维和建筑原则，我创造超越传统边界的体验。', 'sort_order' => 3],
                    ]),
                ]
            );
        }
    }
}
