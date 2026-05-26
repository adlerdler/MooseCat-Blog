<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Users Controller
 * 
 * Handles user management operations.
 * Provides CRUD functionality for system users.
 */
class UsersController extends Controller
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
     * Display the user list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->mockDataService->getUsers();
        $roles = $this->mockDataService->getRoles();
        
        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Display the create user form
     * 
     * @return Response
     */
    public function create(): Response
    {
        $roles = $this->mockDataService->getRoles();
        
        return Inertia::render('admin/Users', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created user
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|integer',
        ]);

        return back()->with('success', '用户已创建');
    }

    /**
     * Display the specified user
     * 
     * @param string $id
     * @return Response
     */
    public function show(string $id): Response
    {
        $users = $this->mockDataService->getUsers();
        $roles = $this->mockDataService->getRoles();
        $authorProfiles = $this->mockDataService->getAuthorProfiles();
        
        return Inertia::render('admin/UserDetail', [
            'users' => $users,
            'roles' => $roles,
            'authorProfiles' => $authorProfiles,
        ]);
    }

    /**
     * Display the edit user form
     * 
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $users = $this->mockDataService->getUsers();
        $user = collect($users)->firstWhere('id', $id);
        $roles = $this->mockDataService->getRoles();
        
        return Inertia::render('admin/Users', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required|integer',
        ]);

        return back()->with('success', '用户已更新');
    }

    /**
     * Remove the specified user
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '用户已删除');
    }
}
