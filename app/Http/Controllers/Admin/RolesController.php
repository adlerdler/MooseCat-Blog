<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Roles Controller
 *
 * Handles role management operations.
 * Provides CRUD functionality for system roles and permissions.
 */
class RolesController extends Controller
{
    protected $mockDataService;

    /**
     * Constructor
     *
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display the role list
     *
     * @return Response
     */
    public function index(): Response
    {
        $roles = $this->mockDataService->getRoles();
        $permissions = $this->mockDataService->getPermissions();
        $rolePermissions = $this->mockDataService->getRolePermissions();

        return Inertia::render('admin/Roles', [
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Display the create role form
     *
     * @return Response
     */
    public function create(): Response
    {
        $permissions = $this->mockDataService->getPermissions();

        return Inertia::render('admin/Roles', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string',
            'permissions' => 'array',
        ]);

        return back()->with('success', '角色已创建');
    }

    /**
     * Display the edit role form
     *
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $roles = $this->mockDataService->getRoles();
        $role = collect($roles)->firstWhere('id', $id);
        $permissions = $this->mockDataService->getPermissions();
        $rolePermissions = $this->mockDataService->getRolePermissions();

        return Inertia::render('admin/Roles', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified role
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        return back()->with('success', '角色已更新');
    }

    /**
     * Remove the specified role
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '角色已删除');
    }
}