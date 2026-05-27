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
            return $tag->delete();
        });
    }

    /**
     * 查找或创建标签
     * Find or create tag
     */
    public function findOrCreate(string $name): Tag
    {
        return Tag::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name)]
        );
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