<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 清空现有权限数据
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $permissions = [
            ['name' => 'manage_posts', 'label' => '文章管理', 'description' => '文章管理权限', 'program_id' => 'content'],
            ['name' => 'manage_videos', 'label' => '视频管理', 'description' => '视频管理权限', 'program_id' => 'content'],
            ['name' => 'manage_projects', 'label' => '项目管理', 'description' => '项目管理权限', 'program_id' => 'content'],
            ['name' => 'manage_resources', 'label' => '资源管理', 'description' => '资源管理权限', 'program_id' => 'content'],
            ['name' => 'manage_journals', 'label' => '日志管理', 'description' => '日志管理权限', 'program_id' => 'content'],
            ['name' => 'manage_categories', 'label' => '分类管理', 'description' => '分类管理权限', 'program_id' => 'content'],
            ['name' => 'manage_tags', 'label' => '标签管理', 'description' => '标签管理权限', 'program_id' => 'content'],
            ['name' => 'manage_comments', 'label' => '评论管理', 'description' => '评论管理权限', 'program_id' => 'comment'],
            ['name' => 'manage_ads', 'label' => '广告管理', 'description' => '广告管理权限', 'program_id' => 'advertisement'],
            ['name' => 'manage_users', 'label' => '用户管理', 'description' => '用户管理权限', 'program_id' => 'user'],
            ['name' => 'manage_subscribers', 'label' => '订阅者管理', 'description' => '订阅者管理权限', 'program_id' => 'user'],
            ['name' => 'manage_user_levels', 'label' => '用户等级', 'description' => '用户等级管理权限', 'program_id' => 'user'],
            ['name' => 'manage_roles', 'label' => '角色权限', 'description' => '角色管理权限', 'program_id' => 'system'],
            ['name' => 'manage_settings', 'label' => '基本设置', 'description' => '系统设置权限', 'program_id' => 'system'],
            ['name' => 'manage_social_login', 'label' => '社交登录', 'description' => '社交登录配置权限', 'program_id' => 'system'],
            ['name' => 'manage_social_links', 'label' => '社交链接', 'description' => '社交链接管理权限', 'program_id' => 'system'],
            ['name' => 'manage_seo', 'label' => 'SEO管理', 'description' => 'SEO管理权限', 'program_id' => 'system'],
            ['name' => 'manage_i18n', 'label' => '国际化', 'description' => '国际化配置权限', 'program_id' => 'system'],
            ['name' => 'manage_media', 'label' => '媒体管理', 'description' => '媒体库管理权限', 'program_id' => 'system'],
            ['name' => 'manage_email_templates', 'label' => '邮件模板', 'description' => '邮件模板管理权限', 'program_id' => 'system'],
            ['name' => 'manage_menu', 'label' => '菜单管理', 'description' => '菜单管理权限', 'program_id' => 'system'],
            ['name' => 'manage_notifications', 'label' => '通知管理', 'description' => '通知管理权限', 'program_id' => 'system'],
            ['name' => 'manage_mail_config', 'label' => '邮件配置', 'description' => '邮件配置权限', 'program_id' => 'system'],
            ['name' => 'manage_logs', 'label' => '系统日志', 'description' => '系统日志查看权限', 'program_id' => 'system'],
            ['name' => 'manage_backup', 'label' => '备份管理', 'description' => '备份管理权限', 'program_id' => 'system'],
            ['name' => 'manage_restore', 'label' => '恢复管理', 'description' => '恢复管理权限', 'program_id' => 'system'],
            ['name' => 'view_analytics', 'label' => '仪表盘', 'description' => '仪表盘查看权限', 'program_id' => 'dashboard'],
        ];

        foreach ($permissions as $perm) {
            Permission::create([
                'name' => $perm['name'],
                'guard_name' => 'web',
                'label' => $perm['label'],
                'description' => $perm['description'],
                'program_id' => $perm['program_id'],
            ]);
        }

        $rolePermissions = [
            // Administrator (role_id: 1) - 所有 27 个权限
            ['role_id' => 1, 'permission_id' => 1], ['role_id' => 1, 'permission_id' => 2],
            ['role_id' => 1, 'permission_id' => 3], ['role_id' => 1, 'permission_id' => 4],
            ['role_id' => 1, 'permission_id' => 5], ['role_id' => 1, 'permission_id' => 6],
            ['role_id' => 1, 'permission_id' => 7], ['role_id' => 1, 'permission_id' => 8],
            ['role_id' => 1, 'permission_id' => 9], ['role_id' => 1, 'permission_id' => 10],
            ['role_id' => 1, 'permission_id' => 11], ['role_id' => 1, 'permission_id' => 12],
            ['role_id' => 1, 'permission_id' => 13], ['role_id' => 1, 'permission_id' => 14],
            ['role_id' => 1, 'permission_id' => 15], ['role_id' => 1, 'permission_id' => 16],
            ['role_id' => 1, 'permission_id' => 17], ['role_id' => 1, 'permission_id' => 18],
            ['role_id' => 1, 'permission_id' => 19], ['role_id' => 1, 'permission_id' => 20],
            ['role_id' => 1, 'permission_id' => 21], ['role_id' => 1, 'permission_id' => 22],
            ['role_id' => 1, 'permission_id' => 23], ['role_id' => 1, 'permission_id' => 24],
            ['role_id' => 1, 'permission_id' => 25], ['role_id' => 1, 'permission_id' => 26],
            ['role_id' => 1, 'permission_id' => 27],
            // Editor (role_id: 2)
            ['role_id' => 2, 'permission_id' => 1], ['role_id' => 2, 'permission_id' => 5],
            ['role_id' => 2, 'permission_id' => 7], ['role_id' => 2, 'permission_id' => 8],
            ['role_id' => 2, 'permission_id' => 15],
            // Author (role_id: 3)
            ['role_id' => 3, 'permission_id' => 1],
            // Moderator (role_id: 4)
            ['role_id' => 4, 'permission_id' => 5], ['role_id' => 4, 'permission_id' => 6],
            ['role_id' => 4, 'permission_id' => 14],
            // Subscriber (role_id: 5)
            ['role_id' => 5, 'permission_id' => 6], ['role_id' => 5, 'permission_id' => 7],
            ['role_id' => 5, 'permission_id' => 8],
            // API (role_id: 6)
            ['role_id' => 6, 'permission_id' => 11],
        ];

        foreach ($rolePermissions as $rp) {
            DB::table('role_has_permissions')->insert($rp);
        }
    }
}
