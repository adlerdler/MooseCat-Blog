<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * VideoRepository - 视频数据访问层
 * 
 * 封装视频相关的复杂查询逻辑，包括分页搜索、分类筛选、热门视频等功能。
 * Encapsulates complex query logic related to videos, including paginated search, 
 * category filtering, popular videos and other functionalities.
 */
class VideoRepository
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
     * 获取分页视频列表（带筛选）
     * Get paginated videos with filters
     */
    public function getPaginatedVideos(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Video::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['tag'] ?? null, fn($q, $slug) => $q->whereHas('tags', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->when($filters['keyword'] ?? null, function ($q) use ($filters) {
                $q->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                      ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                });
            })
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * 搜索视频
     * Search videos
     */
    public function searchVideos(string $keyword, int $limit = 10): Collection
    {
        return Video::query()
            ->with(['category'])
            ->where('status', 'published')
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%');
            })
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据分类获取视频
     * Get videos by category
     */
    public function getVideosByCategory(string $categorySlug, int $limit = null): Collection
    {
        $query = Video::query()
            ->with(['tags'])
            ->whereHas('category', fn($q) => $q->where('slug', $categorySlug))
            ->where('status', 'published')
            ->latest('published_at');

        if ($limit !== null) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * 获取热门视频
     * Get popular videos
     */
    public function getPopularVideos(int $limit = 10): Collection
    {
        return Video::query()
            ->with(['category'])
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 根据slug获取视频
     * Get video by slug
     */
    public function getVideoBySlug(string $slug): ?Video
    {
        return Video::where('slug', $slug)->with(['category', 'tags'])->first();
    }
}