<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthorProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_users')->except(['profile', 'updateProfile']);
    }

    /**
     * 个人资料页面 - 查看/编辑自己的基本信息
     */
    public function profile(string $slug): Response
    {
        $profile = AuthorProfile::where('slug', $slug)->firstOrFail();
        $user = $profile->user()->firstOrFail();

        $profileData = [
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'slug' => $profile->slug,
            'display_name' => $profile->display_name,
            'bio' => $profile->bio,
            'avatar' => $profile->avatar,
            'role_label' => $profile->role_label,
            'role_title' => $profile->role_title,
            'company' => $profile->company,
            'social_links' => is_array($profile->social_links) ? $profile->social_links : (is_string($profile->social_links) ? json_decode($profile->social_links, true) ?? [] : []),
            'skills' => is_array($profile->skills) ? $profile->skills : (is_string($profile->skills) ? json_decode($profile->skills, true) ?? [] : []),
            'status_label' => $profile->status_label,
            'status_text' => $profile->status_text,
            'is_active' => $profile->is_active,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ];

        return Inertia::render('admin/Profile', [
            'profile' => $profileData,
        ]);
    }

    /**
     * 更新个人资料
     */
    public function updateProfile(Request $request, string $slug): RedirectResponse
    {
        $profile = AuthorProfile::where('slug', $slug)->firstOrFail();
        $user = $profile->user()->firstOrFail();

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email,' . $user->id,
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'skills' => 'nullable|array',
            'slug' => 'nullable|string|max:255|unique:author_profiles,slug,' . $profile->id,
        ]);

        // 更新用户信息
        $user->update([
            'name' => $validated['user_name'],
            'email' => $validated['user_email'],
        ]);

        // 处理 slug：如果留空则自动生成 Ar_ + 12位随机字符
        $newSlug = $validated['slug'] ?? '';
        if (empty($newSlug) || trim($newSlug) === '') {
            $newSlug = 'Ar_' . Str::random(12);
            // 确保唯一性
            while (AuthorProfile::where('slug', $newSlug)->where('id', '!=', $profile->id)->exists()) {
                $newSlug = 'Ar_' . Str::random(12);
            }
        }

        // 更新作者资料
        $profile->update([
            'slug' => $newSlug,
            'display_name' => $validated['display_name'] ?? $profile->display_name,
            'bio' => $validated['bio'] ?? $profile->bio,
            'role_label' => $validated['role_label'] ?? $profile->role_label,
            'role_title' => $validated['role_title'] ?? $profile->role_title,
            'company' => $validated['company'] ?? $profile->company,
            'social_links' => $validated['social_links'] ?? $profile->social_links,
            'skills' => $validated['skills'] ?? $profile->skills,
        ]);

        return back()->with('success', '个人资料已更新');
    }

    /**
     * 上传用户头像
     * 文件存储到 public/media/user-ims/，同时加入媒体库
     */
    public function uploadAvatar(Request $request, string $slug): RedirectResponse
    {
        $profile = AuthorProfile::where('slug', $slug)->firstOrFail();
        $user = $profile->user()->firstOrFail();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $file = $request->file('avatar');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());

        // 1. 存储到 public/media/user-ims/ 目录
        $storedPath = Storage::disk('public')->putFileAs('media/user-ims', $file, $filename);

        // 2. 同时加入 Spatie Media Library，使之在媒体库中可见
        $user->addMedia($file)->toMediaCollection('default', 'public');

        // 3. 更新 AuthorProfile 的 avatar 字段为可访问的 URL
        $avatarUrl = Storage::disk('public')->url('media/user-ims/' . $filename);
        $profile->update(['avatar' => $avatarUrl]);

        return back()->with('success', '头像已更新');
    }

    public function index(): Response
    {
        $profiles = AuthorProfile::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'user_id' => $p->user_id,
                'user_name' => $p->user?->name,
                'slug' => $p->slug,
                'display_name' => $p->display_name,
                'bio' => $p->bio,
                'avatar' => $p->avatar,
                'role_label' => $p->role_label,
                'role_title' => $p->role_title,
                'company' => $p->company,
                'status_label' => $p->status_label,
                'status_text' => $p->status_text,
                'is_active' => $p->is_active,
                'social_links' => $p->social_links ?? [],
                'expertise' => $p->expertise ?? [],
                'skills' => $p->skills ?? [],
                'manifestos' => $p->manifestos ?? [],
                'created_at' => $p->created_at?->format('Y-m-d'),
            ]);

        return Inertia::render('admin/AuthorProfiles', [
            'profiles' => $profiles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'slug' => 'required|string|unique:author_profiles,slug',
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'skills' => 'nullable|array',
            'manifestos' => 'nullable|array',
        ]);

        AuthorProfile::create($validated);

        return back()->with('success', '作者资料已创建');
    }

    public function update(Request $request, AuthorProfile $profile): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => 'required|string|unique:author_profiles,slug,' . $profile->id,
            'display_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:255',
            'social_links' => 'nullable|array',
            'skills' => 'nullable|array',
            'manifestos' => 'nullable|array',
        ]);

        $profile->update($validated);

        return back()->with('success', '作者资料已更新');
    }

    public function destroy(AuthorProfile $profile): RedirectResponse
    {
        $profile->delete();

        return back()->with('success', '作者资料已删除');
    }
}