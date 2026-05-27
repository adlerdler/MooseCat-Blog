<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 重置缓存
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 定义权限
        $permissions = [
            // 用户管理
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            
            // 角色管理
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            
            // 文章管理
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'publish-posts',
            
            // 分类管理
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',
            
            // 标签管理
            'view-tags',
            'create-tags',
            'edit-tags',
            'delete-tags',
            
            // 评论管理
            'view-comments',
            'create-comments',
            'edit-comments',
            'delete-comments',
            'approve-comments',
            
            // 媒体管理
            'view-media',
            'upload-media',
            'edit-media',
            'delete-media',
            
            // 项目管理
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',
            
            // 资源管理
            'view-resources',
            'create-resources',
            'edit-resources',
            'delete-resources',
            
            // 视频管理
            'view-videos',
            'create-videos',
            'edit-videos',
            'delete-videos',
            
            // 广告管理
            'view-advertisements',
            'create-advertisements',
            'edit-advertisements',
            'delete-advertisements',
            
            // 订阅者管理
            'view-subscribers',
            'create-subscribers',
            'edit-subscribers',
            'delete-subscribers',
            
            // 系统设置
            'view-settings',
            'edit-settings',
            
            // 菜单管理
            'view-menus',
            'edit-menus',
            
            // SEO管理
            'view-seo',
            'edit-seo',
            
            // 多语言管理
            'view-translations',
            'edit-translations',
            
            // 邮件管理
            'view-email-templates',
            'edit-email-templates',
            'view-mail-config',
            'edit-mail-config',
            
            // 日志管理
            'view-journals',
            'view-logs',
            
            // 备份管理
            'view-backups',
            'create-backups',
            'delete-backups',
            'restore-backups',
        ];

        // 创建权限
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // 创建角色
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ], [
            'value' => 'admin',
            'label' => 'Administrator',
            'color' => 'text-red-500 bg-red-100',
            'description' => '系统管理员，拥有所有权限',
        ]);

        $editorRole = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'web',
        ], [
            'value' => 'editor',
            'label' => 'Editor',
            'color' => 'text-blue-500 bg-blue-100',
            'description' => '编辑，可以管理内容',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ], [
            'value' => 'user',
            'label' => 'User',
            'color' => 'text-gray-500 bg-gray-100',
            'description' => '普通用户',
        ]);

        // 给管理员角色分配所有权限
        $adminRole->syncPermissions(Permission::all());

        // 给编辑角色分配内容相关权限
        $editorRole->syncPermissions([
            'view-posts', 'create-posts', 'edit-posts', 'delete-posts', 'publish-posts',
            'view-categories', 'create-categories', 'edit-categories',
            'view-tags', 'create-tags', 'edit-tags',
            'view-comments', 'approve-comments', 'delete-comments',
            'view-media', 'upload-media', 'edit-media', 'delete-media',
            'view-projects', 'create-projects', 'edit-projects',
            'view-resources', 'create-resources', 'edit-resources',
            'view-videos', 'create-videos', 'edit-videos',
        ]);

        // 给普通用户分配基本权限
        $userRole->syncPermissions([
            'view-posts',
            'view-categories',
            'view-tags',
            'view-comments',
            'create-comments',
        ]);
    }
}
