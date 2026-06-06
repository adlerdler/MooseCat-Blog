<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Video;
use App\Events\SeoFilesNeedRegenerate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * VideoService - 视频服务类
 * 
 * 提供视频内容的管理功能，包括视频列表、创建、更新、删除和浏览量统计。
 * Provides video content management functionality, including video listing, creation, 
 * update, deletion and view count statistics.
 */
class VideoService
{
    public function __construct(protected TagService $tagService)
    {
    }

    /**
     * 获取视频列表（带分页和筛选）
     * Get paginated video list with filters
     */
    public function getPaginatedVideos(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Video::query()
            ->with(['category', 'tags'])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['category_id'] ?? null, fn($q, $id) => $q->where('category_id', $id))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['platform'] ?? null, fn($q, $platform) => $q->where('platform', $platform))
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * 根据slug获取视频详情
     * Get video by slug
     */
    public function getVideoBySlug(string $slug): ?Video
    {
        return Video::where('slug', $slug)->with(['category', 'tags'])->first();
    }

    /**
     * 获取所有视频
     * Get all videos
     */
    public function getVideos(array $filters = []): Collection
    {
        return Video::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->latest('published_at')
            ->get();
    }

    /**
     * 创建视频
     * Create video
     */
    public function createVideo(array $data): Video
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? (Str::random(8) . '-' . Str::random(4));
            $data['status'] = $data['status'] ?? 'draft';
            
            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            // SEO 默认值：未提供时自动从标题/描述/标签填充
            $data['meta_title']       = ($data['meta_title'] ?? '')       ?: ($data['title'] ?? '');
            $data['meta_description'] = ($data['meta_description'] ?? '') ?: ($data['description'] ?? '');
            $data['meta_keywords']    = ($data['meta_keywords'] ?? '')    ?: (is_array($tags) ? implode(', ', $tags) : '');
            
            $video = Video::create($data);

            if (!empty($tags)) {
                $video->tags()->sync($this->tagService->resolveTagIds($tags));
            }

            if ($video->status === 'published') {
                SeoFilesNeedRegenerate::dispatch();
            }

            return $video;
        });
    }

    public function updateVideo(Video $video, array $data): Video
    {
        return DB::transaction(function () use ($video, $data) {
            $wasPublished = $video->status === 'published';
            $nowPublished = ($data['status'] ?? $video->status) === 'published';

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $video->update($data);

            if (!empty($tags)) {
                $video->tags()->sync($this->tagService->resolveTagIds($tags));
            }

            if ($wasPublished || $nowPublished) {
                SeoFilesNeedRegenerate::dispatch();
            }

            return $video;
        });
    }

    /**
     * 删除视频
     * Delete video
     */
    public function deleteVideo(Video $video): bool
    {
        return DB::transaction(function () use ($video) {
            $wasPublished = $video->status === 'published';
            $video->tags()->detach();
            $result = $video->delete();

            if ($wasPublished) {
                SeoFilesNeedRegenerate::dispatch();
            }

            return $result;
        });
    }

    /**
     * 增加视频观看量
     * Increment video view count
     */
    public function incrementViews(Video $video): void
    {
        $video->increment('views_count');
    }
}