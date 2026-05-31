<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): Response
    {
        $users = User::with(['roles', 'userLevel'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar' => $u->avatar,
                'status' => $u->status,
                'points' => $u->points,
                'level_name' => $u->userLevel?->name,
                'roles' => $u->roles->pluck('name')->toArray(),
                'posts_count' => $u->posts()->count(),
                'comments_count' => $u->comments()->count(),
                'created_at' => $u->created_at?->format('Y-m-d'),
                'last_login_at' => $u->last_login_at?->format('Y-m-d'),
            ]);

        $roles = Role::orderBy('name')->get(['id', 'name'])->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'label' => ucfirst($r->name),
        ]);

        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function show(User $user): Response
    {
        $user->load(['roles', 'userLevel', 'posts', 'comments', 'authorProfile']);

        $profile = $user->authorProfile;

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'bio' => $profile?->bio,
            'github' => ($profile?->social_links ?? [])['github'] ?? null,
            'twitter' => ($profile?->social_links ?? [])['twitter'] ?? null,
            'linkedin' => ($profile?->social_links ?? [])['linkedin'] ?? null,
            'status' => $user->status,
            'points' => $user->points,
            'level_name' => $user->userLevel?->name,
            'roles' => $user->roles->pluck('name')->toArray(),
            'posts_count' => $user->posts->count(),
            'comments_count' => $user->comments->count(),
            'created_at' => $user->created_at?->format('Y-m-d'),
            'last_login_at' => $user->last_login_at?->format('Y-m-d'),
            'skills' => is_array($profile?->skills) ? $profile->skills : (is_string($profile?->skills) ? json_decode($profile->skills, true) ?? [] : []),
            'social_links' => is_array($profile?->social_links) ? $profile->social_links : (is_string($profile?->social_links) ? json_decode($profile->social_links, true) ?? [] : []),
            'manifestos' => is_array($profile?->manifestos) ? $profile->manifestos : (is_string($profile?->manifestos) ? json_decode($profile->manifestos, true) ?? [] : []),
            'posts' => $user->posts->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'status' => $p->status,
                'created_at' => $p->created_at?->format('Y-m-d'),
            ])->toArray(),
        ];

        $roles = Role::orderBy('name')->get(['id', 'name'])->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'label' => ucfirst($r->name),
        ]);

        return Inertia::render('admin/UserDetail', [
            'user' => $userData,
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->userService->createUser($data);

        return back()->with('success', '用户已创建');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $this->userService->updateUser($user, $data);

        return back()->with('success', '用户已更新');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->deleteUser($user);

        return back()->with('success', '用户已删除');
    }
}