<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;

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
     * Get all roles with associated permissions.
     */
    public function getAllRoles(): array
    {
        return Role::with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id'               => $r->id,
                'name'             => $r->name,
                'label'            => $r->label ?? $r->name,
                'color'            => $r->color ?? 'gray',
                'description'      => $r->description ?? '',
                'guard_name'       => $r->guard_name,
                'permissions'      => $r->permissions->pluck('id')->toArray(),
                'permissions_count' => $r->permissions->count(),
                'created_at'       => $r->created_at?->format('Y-m-d'),
            ])->toArray();
    }

    /**
     * 获取所有权限列表
     * Get all permissions list.
     */
    public function getAllPermissions(): Collection
    {
        return Permission::orderBy('program_id')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'label'       => $p->label ?? $p->name,
                'description' => $p->description ?? '',
                'program_id'  => $p->program_id ?? 'general',
                'guard_name'  => $p->guard_name,
            ]);
    }

    /**
     * 创建角色并同步权限
     * Create a role and sync permissions.
     */
    public function create(array $data): Role
    {
        $role = Role::create([
            'name'        => $data['name'],
            'label'       => $data['label'] ?? $data['name'],
            'color'       => $data['color'] ?? 'gray',
            'description' => $data['description'] ?? '',
            'guard_name'  => $data['guard_name'],
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * 更新角色并同步权限
     * Update a role and sync permissions.
     */
    public function update(Role $role, array $data): Role
    {
        $role->update([
            'name'        => $data['name'],
            'label'       => $data['label'] ?? $data['name'],
            'color'       => $data['color'] ?? 'gray',
            'description' => $data['description'] ?? '',
            'guard_name'  => $data['guard_name'],
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * 删除角色
     * Delete a role.
     */
    public function delete(Role $role): void
    {
        $role->delete();
    }
}
