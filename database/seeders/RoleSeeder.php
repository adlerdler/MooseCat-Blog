<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'value' => 'admin',
                'guard_name' => 'web',
                'label' => '管理员',
                'color' => '#ef4444',
                'description' => '系统管理员，拥有所有权限',
            ],
            [
                'name' => 'Editor',
                'value' => 'editor',
                'guard_name' => 'web',
                'label' => '编辑',
                'color' => '#3b82f6',
                'description' => '内容编辑，可以管理文章和评论',
            ],
            [
                'name' => 'Author',
                'value' => 'author',
                'guard_name' => 'web',
                'label' => '作者',
                'color' => '#10b981',
                'description' => '文章作者，可以创建和编辑自己的文章',
            ],
            [
                'name' => 'Moderator',
                'value' => 'moderator',
                'guard_name' => 'web',
                'label' => '版主',
                'color' => '#8b5cf6',
                'description' => '社区版主，可以管理评论和用户',
            ],
            [
                'name' => 'Subscriber',
                'value' => 'subscriber',
                'guard_name' => 'web',
                'label' => '订阅者',
                'color' => '#6b7280',
                'description' => '普通订阅者，可以浏览和评论内容',
            ],
            [
                'name' => 'API',
                'value' => 'api',
                'guard_name' => 'api',
                'label' => 'API用户',
                'color' => '#eab308',
                'description' => 'API用户，用于程序访问',
            ],
            [
                'name' => 'Guest',
                'value' => 'guest',
                'guard_name' => 'web',
                'label' => '访客',
                'color' => '#06b6d4',
                'description' => '访客用户，只读权限',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
