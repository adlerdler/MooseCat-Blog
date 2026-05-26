<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RolesController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $roles = $this->mockDataService->getRoles();
        $permissions = $this->mockDataService->getPermissions();
        
        return Inertia::render('admin/Roles', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function create(): Response
    {
        $permissions = $this->mockDataService->getPermissions();
        
        return Inertia::render('admin/Roles', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string',
            'permissions' => 'array',
        ]);

        return back()->with('success', '角色已创建');
    }

    public function edit(string $id): Response
    {
        $roles = $this->mockDataService->getRoles();
        $role = collect($roles)->firstWhere('id', $id);
        $permissions = $this->mockDataService->getPermissions();
        
        return Inertia::render('admin/Roles', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        return back()->with('success', '角色已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '角色已删除');
    }
}