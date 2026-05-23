<?php

namespace Database\Seeders;

use App\Models\Role;
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
                'description' => '内容编辑，可以管理文章和资源',
            ],
            [
                'name' => 'Author',
                'value' => 'author',
                'guard_name' => 'web',
                'label' => '作者',
                'color' => '#10b981',
                'description' => '内容作者，可以创建和管理自己的内容',
            ],
            [
                'name' => 'Subscriber',
                'value' => 'subscriber',
                'guard_name' => 'web',
                'label' => '订阅者',
                'color' => '#6b7280',
                'description' => '普通订阅者，可以浏览和评论内容',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
