<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * TagRepository - 标签数据访问层
 * 
 * 封装标签相关的复杂查询逻辑，包括热门标签、文章数统计等功能。
 * Encapsulates complex query logic related to tags, including popular tags, 
 * post count statistics and other functionalities.
 */
class TagRepository
{
    /**
     * 创建新的数据访问层实例
     * Create a new repository instance
     */
    public function __construct()
    {
        //
    }

    /**
     * 获取所有标签（带关联数量）
     * Get all tags with relation counts
     */
    public function getTagsWithCount(): Collection
    {
        return Tag::query()
            ->withCount(['posts', 'videos', 'projects'])
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
     * 获取有内容的标签
     * Get tags that have content
     */
    public function getTagsWithContent(): Collection
    {
        return Tag::query()
            ->withCount(['posts', 'videos', 'projects'])
            ->having(function ($query) {
                $query->havingRaw('posts_count + videos_count + projects_count > 0');
            })
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * 搜索标签
     * Search tags
     */
    public function searchTags(string $keyword, int $limit = 10): Collection
    {
        return Tag::query()
            ->where('name', 'like', '%' . $keyword . '%')
            ->orWhere('slug', 'like', '%' . $keyword . '%')
            ->orderBy('name', 'asc')
            ->limit($limit)
            ->get();
    }
}