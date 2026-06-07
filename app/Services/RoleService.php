<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * RoleService - 角色管理服务类
 *
 * 提供角色的 CRUD 和权限分配功能（基于 Spatie Permission RBAC）。
 * Provides role CRUD and permission assignment (based on Spatie Permission RBAC).
 */
class RoleService
{
    public function __construct() {}

    /**
     * 获取所有角色（含关联权限）
     */
    public function getAllRoles(): array
    {
        return Role::with('permissions')
            ->orderBy('name')
            ->get()
            ->map(function($r) {
                // 获取该角色对应 guard 的权限 ID
                $permissionIds = $r->permissions->pluck('id')->toArray();
                
                // 如果没有权限，或者权限的 guard 与角色不一致
                // 我们尝试查找相同名称的 web guard 权限用于显示
                if (empty($permissionIds)) {
                    return [
                        'id'               => $r->id,
                        'name'             => $r->name,
                        'label'            => $r->label ?? $r->name,
                        'color'            => $r->color ?? 'gray',
                        'description'      => $r->description ?? '',
                        'guard_name'       => $r->guard_name,
                        'permissions'      => [],
                        'permissions_count' => 0,
                        'created_at'       => $r->created_at?->format('Y-m-d'),
                    ];
                }

                // 为了前端显示，我们需要将 guard 特定的权限映射到 web guard 的权限 ID
                $webPermissionIds = [];
                foreach ($r->permissions as $perm) {
                    $webPerm = Permission::where('name', $perm->name)
                        ->where('guard_name', 'web')
                        ->first();
                    if ($webPerm) {
                        $webPermissionIds[] = $webPerm->id;
                    }
                }

                return [
                    'id'               => $r->id,
                    'name'             => $r->name,
                    'label'            => $r->label ?? $r->name,
                    'color'            => $r->color ?? 'gray',
                    'description'      => $r->description ?? '',
                    'guard_name'       => $r->guard_name,
                    'permissions'      => !empty($webPermissionIds) ? $webPermissionIds : $permissionIds,
                    'permissions_count' => $r->permissions->count(),
                    'created_at'       => $r->created_at?->format('Y-m-d'),
                ];
            })->toArray();
    }

    /**
     * 获取所有权限列表
     * Get all permissions list.
     */
    public function getAllPermissions(): Collection
    {
        // 获取所有权限，按 name 去重，保留 web guard 的版本
        $permissions = Permission::orderBy('program_id')
            ->orderBy('name')
            ->get();

        // 按 name 分组，每个 name 只保留一个
        $uniquePermissions = $permissions->unique('name');

        return $uniquePermissions->map(fn($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'label'       => $p->label ?? $p->name,
            'description' => $p->description ?? '',
            'program_id'  => $p->program_id ?? 'general',
            'guard_name'  => $p->guard_name,
        ])->values();
    }

    /**
     * 确保权限在指定 guard 中存在
     */
    protected function ensurePermissionsExistInGuard(array $permissionIds, string $guardName): array
    {
        $mappedIds = [];

        foreach ($permissionIds as $permissionId) {
            $permission = Permission::find($permissionId);

            if ($permission) {
                // 查找或创建该 guard 对应的权限
                $guardPermission = Permission::where('name', $permission->name)
                    ->where('guard_name', $guardName)
                    ->first();

                if (!$guardPermission) {
                    // 在目标 guard 中创建权限
                    $guardPermission = Permission::create([
                        'name'        => $permission->name,
                        'label'       => $permission->label,
                        'description' => $permission->description,
                        'program_id'  => $permission->program_id,
                        'guard_name'  => $guardName,
                    ]);
                }

                $mappedIds[] = $guardPermission->id;
            }
        }

        return $mappedIds;
    }

    /**
     * 创建角色并同步权限
     */
    public function create(array $data): Role
    {
        $guardName = $this->resolveGuardName($data['name'], $data['guard_name']);

        $role = Role::create([
            'name'        => $data['name'],
            'label'       => $data['label'] ?? $data['name'],
            'color'       => $data['color'] ?? 'gray',
            'description' => $data['description'] ?? '',
            'guard_name'  => $guardName,
        ]);

        if (isset($data['permissions']) && !empty($data['permissions'])) {
            $mappedPermissionIds = $this->ensurePermissionsExistInGuard($data['permissions'], $guardName);
            $role->syncPermissions($mappedPermissionIds);
        }

        return $role;
    }

    /**
     * 更新角色并同步权限
     */
    public function update(Role $role, array $data): Role
    {
        $guardName = $this->resolveGuardName($data['name'], $data['guard_name']);

        $role->update([
            'name'        => $data['name'],
            'label'       => $data['label'] ?? $data['name'],
            'color'       => $data['color'] ?? 'gray',
            'description' => $data['description'] ?? '',
            'guard_name'  => $guardName,
        ]);

        if (isset($data['permissions']) && !empty($data['permissions'])) {
            $mappedPermissionIds = $this->ensurePermissionsExistInGuard($data['permissions'], $guardName);
            $role->syncPermissions($mappedPermissionIds);
        }

        return $role;
    }

    /**
     * Administrator 必须与后台登录守卫（web）一致，禁止误设为 admin
     */
    protected function resolveGuardName(string $roleName, string $guardName): string
    {
        if ($roleName === User::SUPER_ADMIN_ROLE) {
            return 'web';
        }

        return $guardName;
    }

    /**
     * 删除角色
     */
    public function delete(Role $role): void
    {
        $role->delete();
    }
}
