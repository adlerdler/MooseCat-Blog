<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuthorProfile;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('permission:manage_users');
    }

    public function index(): Response
    {
        $users = User::with(['roles', 'userLevel', 'authorProfile'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar' => $u->authorProfile?->avatar ?? $u->avatar,
                'slug' => $u->authorProfile?->slug ?? Str::slug($u->name),
                'status' => $u->status,
                'points' => $u->points,
                'level_name' => $u->userLevel?->name,
                'roles' => $u->roles->pluck('name')->toArray(),
                'posts_count' => $u->posts()->count(),
                'comments_count' => $u->comments()->count(),
                'created_at' => $u->created_at?->format('Y-m-d'),
                'last_login_at' => $u->last_login_at?->format('Y-m-d'),
            ]);

        $roles = Role::orderBy('name')->get(['id', 'name', 'color'])->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'color' => $r->color ?? 'gray',
            'label' => ucfirst($r->name),
        ]);

        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function show(string $slug): Response
    {
        $profile = AuthorProfile::where('slug', $slug)->firstOrFail();
        $user = $profile->user()->with(['roles', 'userLevel', 'posts', 'comments'])->firstOrFail();

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'slug' => $profile->slug,
            'avatar' => $profile->avatar ?? $user->avatar,
            'bio' => $profile?->bio,
            'github' => ($profile?->social_links ?? [])['github'] ?? null,
            'twitter' => ($profile?->social_links ?? [])['twitter'] ?? null,
            'linkedin' => ($profile?->social_links ?? [])['linkedin'] ?? null,
            'status' => $user->status,
            'points' => $user->points,
            'level_name' => $user->userLevel?->name,
            'role_id' => $user->roles->first()?->id ?? null,
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

        $roles = Role::orderBy('name')->get(['id', 'name', 'color'])->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'color' => $r->color ?? 'gray',
            'label' => ucfirst($r->name),
        ]);

        // 媒体库数据（供头像选择器使用）
        $mediaItems = SpatieMedia::where('collection_name', 'default')
            ->latest()
            ->get()
            ->map(function (SpatieMedia $item) {
                $ext = pathinfo($item->file_name, PATHINFO_EXTENSION);
                return [
                    'id'   => $item->uuid,
                    'name' => $item->name,
                    'type' => str_starts_with($item->mime_type ?? '', 'image/') ? 'image' :
                             (str_starts_with($item->mime_type ?? '', 'video/') ? 'video' : 'document'),
                    'size' => $this->formatSize($item->size ?? 0),
                    'url'  => url("/media/{$item->uuid}" . ($ext ? ".{$ext}" : '')),
                    'date' => $item->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('admin/UserDetail', [
            'user' => $userData,
            'roles' => $roles,
            'media' => $mediaItems,
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

    private function formatSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            abort(403, '不能删除自己的账号');
        }

        $this->userService->deleteUser($user);

        return back()->with('success', '用户已删除');
    }
}