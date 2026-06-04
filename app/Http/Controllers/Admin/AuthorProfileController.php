<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorProfileRequest;
use App\Http\Requests\UpdateAuthorProfileRequest;
use App\Models\AuthorProfile;
use App\Services\AuthorProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthorProfileController extends Controller
{
    public function __construct(
        protected AuthorProfileService $authorProfileService,
    ) {
        $this->middleware('permission:manage_users')->except(['profile', 'updateProfile']);
    }

    /**
     * 个人资料页面 - 查看/编辑自己的基本信息
     */
    public function profile(string $slug): Response
    {
        return Inertia::render('admin/Profile', [
            'profile' => $this->authorProfileService->getFormattedProfile($slug),
        ]);
    }

    /**
     * 更新个人资料
     */
    public function updateProfile(Request $request, string $slug): RedirectResponse
    {
        $profile = $this->authorProfileService->getBySlug($slug);
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

        $this->authorProfileService->updateProfile($slug, $validated);

        return back()->with('success', '个人资料已更新');
    }

    /**
     * 上传用户头像
     */
    public function uploadAvatar(Request $request, string $slug): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $this->authorProfileService->uploadAvatar($slug, $request->file('avatar'));

        return back()->with('success', '头像已更新');
    }

    public function index(): Response
    {
        return Inertia::render('admin/AuthorProfiles', [
            'profiles' => $this->authorProfileService->getAll(),
        ]);
    }

    public function store(StoreAuthorProfileRequest $request): RedirectResponse
    {
        $this->authorProfileService->create($request->validated());

        return back()->with('success', '作者资料已创建');
    }

    public function update(UpdateAuthorProfileRequest $request, AuthorProfile $profile): RedirectResponse
    {
        $this->authorProfileService->update($profile, $request->validated());

        return back()->with('success', '作者资料已更新');
    }

    public function destroy(AuthorProfile $profile): RedirectResponse
    {
        $this->authorProfileService->delete($profile);

        return back()->with('success', '作者资料已删除');
    }
}
