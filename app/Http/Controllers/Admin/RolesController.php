<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Role;

class RolesController extends Controller
{
    public function __construct(
        protected RoleService $roleService,
    ) {
        $this->middleware('permission:manage_roles');
    }

    public function index(): Response
    {
        return Inertia::render('admin/Roles', [
            'roles'       => $this->roleService->getAllRoles(),
            'permissions' => $this->roleService->getAllPermissions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'guard_name' => 'required|string|in:web,api,admin',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $this->roleService->create($validated);

        return back()->with('success', '角色已创建');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'guard_name' => 'required|string|in:web,api,admin',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $this->roleService->update($role, $validated);

        return back()->with('success', '角色已更新');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->roleService->delete($role);

        return back()->with('success', '角色已删除');
    }
}
