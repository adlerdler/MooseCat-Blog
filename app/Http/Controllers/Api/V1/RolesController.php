<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncPermissionsRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->get()
            ->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
            ]);

        return response()->json([
            'roles' => $roles,
        ]);
    }

    public function show(Request $request, Role $role): JsonResponse
    {
        $role->load('permissions');

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

    public function permissions(Request $request): JsonResponse
    {
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
            ->get()
            ->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
            ]);

        return response()->json([
            'permissions' => $permissions,
        ]);
    }

    public function syncPermissions(SyncPermissionsRequest $request, Role $role): JsonResponse
    {
        $permissions = $request->validated()['permissions'] ?? [];

        $role->syncPermissions($permissions);

        $role->load('permissions');

        return response()->json([
            'message' => 'Permissions synced successfully',
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
            ],
        ]);
    }
}
