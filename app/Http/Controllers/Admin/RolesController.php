<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    public function index(): Response
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'label' => $r->label ?? $r->name,
                'color' => $r->color ?? 'gray',
                'description' => $r->description ?? '',
                'guard_name' => $r->guard_name,
                'permissions' => $r->permissions->pluck('id')->toArray(),
                'permissions_count' => $r->permissions->count(),
                'created_at' => $r->created_at?->format('Y-m-d'),
            ]);

        $permissions = Permission::orderBy('program_id')
                ->orderBy('name')
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'label' => $p->label ?? $p->name,
                    'description' => $p->description ?? '',
                    'program_id' => $p->program_id ?? 'general',
                    'guard_name' => $p->guard_name,
                ]);

        return Inertia::render('admin/Roles', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'guard_name' => 'required|string|in:web,admin',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'label' => $validated['label'] ?? $validated['name'],
            'color' => $validated['color'] ?? 'gray',
            'description' => $validated['description'] ?? '',
            'guard_name' => $validated['guard_name'],
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return back()->with('success', '角色已创建');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'label' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'guard_name' => 'required|string|in:web,admin',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'label' => $validated['label'] ?? $validated['name'],
            'color' => $validated['color'] ?? 'gray',
            'description' => $validated['description'] ?? '',
            'guard_name' => $validated['guard_name'],
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return back()->with('success', '角色已更新');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return back()->with('success', '角色已删除');
    }
}