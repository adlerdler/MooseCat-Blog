<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * PostRepository - 文章数据访问层
 * 
 * 封装文章相关的复杂查询逻辑，包括分页搜索、分类筛选、标签筛选等功能。
 * Encapsulates complex query logic related to posts, including paginated search, 
 * category filtering, tag filtering and other functionalities.
 */
class PostRepository
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
     * 获取分页文章列表（带筛选）
     * Get paginated posts with filters
     */
    public function getPaginatedPosts(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Post::query()
            ->with(['author', 'category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['tag'] ?? null, fn($q, $slug) => $q->whereHas('tags', fn($q) => $q->where('slug', $slug)))
            ->when($filters['author'] ?? null, fn($q, $authorId) => $q->where('author_id', $authorId))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->when($filters['keyword'] ?? null, function ($q) use ($filters) {
                $q->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                      ->orWhere('content', 'like', '%' . $filters['keyword'] . '%')
                      ->orWhere('excerpt', 'like', '%' . $filters['keyword'] . '%');
                });
            })
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * 搜索文章
     * Search posts
     */
    public function searchPosts(string $keyword, int $limit = 10): Collection
    {
        return Post::query()
            ->with(['author', 'category'])
            ->where('status', 'published')
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('content', 'like', '%' . $keyword . '%')
                  ->orWhere('excerpt', 'like', '%' . $keyword . '%');
            })
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据分类获取文章
     * Get posts by category
     */
    public function getPostsByCategory(string $categorySlug, int $limit = null): Collection
    {
        $query = Post::query()
            ->with(['author', 'tags'])
            ->whereHas('category', fn($q) => $q->where('slug', $categorySlug))
            ->where('status', 'published')
            ->latest('published_at');

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 根据标签获取文章
     * Get posts by tag
     */
    public function getPostsByTag(string $tagSlug, int $limit = null): Collection
    {
        $query = Post::query()
            ->with(['author', 'category'])
            ->whereHas('tags', fn($q) => $q->where('slug', $tagSlug))
            ->where('status', 'published')
            ->latest('published_at');

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 获取热门文章
     * Get popular posts
     */
    public function getPopularPosts(int $limit = 10): Collection
    {
        return Post::query()
            ->with(['author', 'category'])
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 获取相关文章
     * Get related posts
     */
    public function getRelatedPosts(Post $post, int $limit = 5): Collection
    {
        $tagIds = $post->tags()->pluck('tags.id');

        return Post::query()
            ->with(['author', 'category'])
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagIds))
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 获取归档文章列表（按年月分组）
     * Get archived posts grouped by year and month
     */
    public function getArchivedPosts(): Collection
    {
        return Post::query()
            ->with(['author'])
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
            ->groupByRaw('YEAR(published_at), MONTH(published_at)')
            ->orderByRaw('YEAR(published_at) DESC, MONTH(published_at) DESC')
            ->get();
    }
}