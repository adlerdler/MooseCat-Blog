<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $users = $this->mockDataService->getUsers();
        $roles = $this->mockDataService->getRoles();
        
        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        $roles = $this->mockDataService->getRoles();
        
        return Inertia::render('admin/Users', [
            'roles' => $roles,
        ]);
    }

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

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role_id' => 'required|integer',
        ]);

        return back()->with('success', '用户已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '用户已删除');
    }
}