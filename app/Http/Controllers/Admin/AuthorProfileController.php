<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthorProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_users');
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
                'bio' => $p->bio,
                'avatar' => $p->avatar,
                'role_label' => $p->role_label,
                'role_title' => $p->role_title,
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
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
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
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
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