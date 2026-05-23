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
            AuthorProfile::create([
                'user_id' => $user->id,
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
                'skills' => json_encode(['后端开发', '前端开发', '数据库设计']),
                'manifestos' => json_encode(['分享技术，记录生活']),
            ]);
        }
    }
}
