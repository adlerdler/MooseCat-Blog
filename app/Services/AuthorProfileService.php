<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuthorProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * AuthorProfileService - 作者资料服务类
 *
 * 提供作者资料的 CRUD、头像上传、slug 生成等功能。
 * Provides author profile CRUD, avatar upload, and slug generation.
 */
class AuthorProfileService
{
    public function __construct() {}

    /**
     * 获取所有作者资料列表（含关联用户信息）
     * Get all author profiles with associated user info.
     */
    public function getAll(): array
    {
        return AuthorProfile::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'user_id'      => $p->user_id,
                'user_name'    => $p->user?->name,
                'slug'         => $p->slug,
                'display_name' => $p->display_name,
                'bio'          => $p->bio,
                'avatar'       => $p->avatar,
                'role_label'   => $p->role_label,
                'role_title'   => $p->role_title,
                'company'      => $p->company,
                'status_label' => $p->status_label,
                'status_text'  => $p->status_text,
                'is_active'    => $p->is_active,
                'social_links' => $p->social_links ?? [],
                'expertise'    => $p->expertise ?? [],
                'skills'       => $p->skills ?? [],
                'manifestos'   => $p->manifestos ?? [],
                'created_at'   => $p->created_at?->format('Y-m-d'),
            ])->toArray();
    }

    /**
     * 按 slug 获取单个作者资料（含用户信息）
     * Get a single author profile by slug with user info.
     */
    public function getBySlug(string $slug): AuthorProfile
    {
        return AuthorProfile::where('slug', $slug)->firstOrFail();
    }

    /**
     * 获取格式化的作者资料数据（用于前端渲染）
     * Get formatted profile data for frontend rendering.
     */
    public function getFormattedProfile(string $slug): array
    {
        $profile = $this->getBySlug($slug);
        $user = $profile->user()->firstOrFail();

        return [
            'id'           => $profile->id,
            'user_id'      => $profile->user_id,
            'slug'         => $profile->slug,
            'display_name' => $profile->display_name,
            'bio'          => $profile->bio,
            'avatar'       => $profile->avatar,
            'role_label'   => $profile->role_label,
            'role_title'   => $profile->role_title,
            'company'      => $profile->company,
            'social_links' => is_array($profile->social_links)
                ? $profile->social_links
                : (is_string($profile->social_links) ? json_decode($profile->social_links, true) ?? [] : []),
            'skills'       => is_array($profile->skills)
                ? $profile->skills
                : (is_string($profile->skills) ? json_decode($profile->skills, true) ?? [] : []),
            'status_label' => $profile->status_label,
            'status_text'  => $profile->status_text,
            'is_active'    => $profile->is_active,
            'user_name'    => $user->name,
            'user_email'   => $user->email,
        ];
    }

    /**
     * 创建作者资料
     * Create a new author profile.
     */
    public function create(array $data): AuthorProfile
    {
        return AuthorProfile::create($data);
    }

    /**
     * 更新作者资料
     * Update an existing author profile.
     */
    public function update(AuthorProfile $profile, array $data): AuthorProfile
    {
        $profile->update($data);
        return $profile;
    }

    /**
     * 更新作者资料（含关联用户信息 + slug 自动生成）
     * Update profile with associated user info and auto slug generation.
     */
    public function updateProfile(string $slug, array $data): AuthorProfile
    {
        $profile = $this->getBySlug($slug);
        $user = $profile->user()->firstOrFail();

        // 更新用户基本信息
        if (isset($data['user_name']) || isset($data['user_email'])) {
            $user->update([
                'name'  => $data['user_name'] ?? $user->name,
                'email' => $data['user_email'] ?? $user->email,
            ]);
        }

        // 处理 slug：留空则自动生成 Ar_ + 12位随机字符
        if (empty($data['slug']) || trim((string) $data['slug']) === '') {
            $data['slug'] = 'Ar_' . Str::random(12);
            while (AuthorProfile::where('slug', $data['slug'])->where('id', '!=', $profile->id)->exists()) {
                $data['slug'] = 'Ar_' . Str::random(12);
            }
        }

        $profile->update([
            'slug'         => $data['slug'] ?? $profile->slug,
            'display_name' => $data['display_name'] ?? $profile->display_name,
            'bio'          => $data['bio'] ?? $profile->bio,
            'role_label'   => $data['role_label'] ?? $profile->role_label,
            'role_title'   => $data['role_title'] ?? $profile->role_title,
            'company'      => $data['company'] ?? $profile->company,
            'social_links' => $data['social_links'] ?? $profile->social_links,
            'skills'       => $data['skills'] ?? $profile->skills,
        ]);

        return $profile;
    }

    /**
     * 上传用户头像并同步到媒体库
     * Upload user avatar and sync to media library.
     */
    public function uploadAvatar(string $slug, UploadedFile $file): string
    {
        $profile = $this->getBySlug($slug);
        $user = $profile->user()->firstOrFail();

        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());

        // 存储到 public/media/user-ims/
        Storage::disk('public')->putFileAs('media/user-ims', $file, $filename);

        // 同步到 Spatie Media Library
        $user->addMedia($file)->toMediaCollection('default', 'public');

        // 更新 avatar 字段
        $avatarUrl = Storage::disk('public')->url('media/user-ims/' . $filename);
        $profile->update(['avatar' => $avatarUrl]);

        return $avatarUrl;
    }

    /**
     * 删除作者资料
     * Delete an author profile.
     */
    public function delete(AuthorProfile $profile): void
    {
        $profile->delete();
    }
}
