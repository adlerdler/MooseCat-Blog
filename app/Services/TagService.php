<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * TagService - 标签服务类
 * 
 * 提供文章标签的管理功能，包括标签列表、创建、更新、删除和批量创建。
 * Provides tag management functionality, including tag listing, creation, update, 
 * deletion and batch creation.
 */
class TagService
{
    /**
     * 获取所有标签（带关联数量）
     * Get all tags with relation counts
     */
    public function getTags(): Collection
    {
        return Tag::query()
            ->withCount(['posts', 'videos'])
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * 获取热门标签
     * Get popular tags
     */
    public function getPopularTags(int $limit = 20): Collection
    {
        return Tag::query()
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据slug获取标签
     * Get tag by slug
     */
    public function getTagBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    /**
     * 创建标签
     * Create tag
     */
    public function createTag(array $data): Tag
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            return Tag::create($data);
        });
    }

    /**
     * 更新标签
     * Update tag
     */
    public function updateTag(Tag $tag, array $data): Tag
    {
        return DB::transaction(function () use ($tag, $data) {
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            $tag->update($data);
            return $tag;
        });
    }

    /**
     * 删除标签
     * Delete tag
     */
    public function deleteTag(Tag $tag): bool
    {
        return DB::transaction(function () use ($tag) {
            $tag->posts()->detach();
            $tag->videos()->detach();
            $tag->projects()->detach();
            $tag->resources()->detach();
            return $tag->delete();
        });
    }

    /**
     * 查找或创建标签（按 name 或 slug 匹配）
     * Find or create tag (match by name or slug)
     */
    public function findOrCreate(string $name): Tag
    {
        $trimmed = trim($name);
        $slug = Str::slug($trimmed);

        // 1. 按 name 精确匹配
        $tag = Tag::where('name', $trimmed)->first();
        if ($tag) return $tag;

        // 2. 按 slug 匹配（兼容前端传 URL 友好格式）
        $tag = Tag::where('slug', $slug)->first();
        if ($tag) return $tag;

        // 3. 都不存在 → 新建
        return Tag::create([
            'name' => $trimmed,
            'slug' => $slug,
        ]);
    }

    /**
     * 统一解析标签数组为 ID 数组（字符串名自动 findOrCreate）
     * Resolve tag array to ID array (auto findOrCreate for string names)
     */
    public function resolveTagIds(array $tags): array
    {
        return collect($tags)
            ->map(function ($tag) {
                if (is_numeric($tag)) {
                    return (int) $tag;
                }
                return $this->findOrCreate($tag)->id;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * 批量创建标签
     * Batch create tags
     */
    public function createTags(array $names): SupportCollection
    {
        $tags = [];
        foreach ($names as $name) {
            $tags[] = $this->findOrCreate(trim($name));
        }
        return new SupportCollection($tags);
    }
}