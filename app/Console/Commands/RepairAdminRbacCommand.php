<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * 一次性修复管理员 RBAC（不重跑 Seeder）
 * - 将 Administrator 角色归一到 web 守卫
 * - 同步 web 权限到 Administrator
 * - 为指定管理员账号补上角色关联
 */
class RepairAdminRbacCommand extends Command
{
    protected $signature = 'rbac:repair-admin
                            {--email=admin@arkhyx.com : 需要修复的管理员邮箱}';

    protected $description = '修复 Administrator 角色守卫与用户关联（无需重跑 Seeder）';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = $this->normalizeAdministratorRole();
        if (!$role) {
            $this->error('未找到 Administrator 角色，请先确保 roles 表有数据。');

            return self::FAILURE;
        }

        $webPermissionNames = Permission::where('guard_name', 'web')->pluck('name')->all();
        $role->syncPermissions($webPermissionNames);
        $this->info("已同步 Administrator 的 web 权限（共 " . count($webPermissionNames) . ' 项）');

        $email = (string) $this->option('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->warn("用户 {$email} 不存在，已跳过角色分配。");

            return self::SUCCESS;
        }

        if (!$user->isAdministrator()) {
            $user->syncRoles([$role]);
            $this->info("已为 {$email} 分配 Administrator 角色。");
        } else {
            $this->info("{$email} 已具备 Administrator 角色，无需重复分配。");
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->info('RBAC 修复完成，请重新登录后台。');

        return self::SUCCESS;
    }

    private function normalizeAdministratorRole(): ?Role
    {
        $webRole = Role::where('name', User::SUPER_ADMIN_ROLE)
            ->where('guard_name', 'web')
            ->first();

        if ($webRole) {
            return $webRole;
        }

        $legacyRole = Role::where('name', User::SUPER_ADMIN_ROLE)->first();
        if (!$legacyRole) {
            return null;
        }

        $fromGuard = $legacyRole->guard_name;
        $legacyRole->update(['guard_name' => 'web']);
        $this->warn("已将 Administrator 守卫从 {$fromGuard} 修正为 web。");

        return $legacyRole->fresh();
    }
}
