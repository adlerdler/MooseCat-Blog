<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Video;
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
    /**
     * 获取视频列表（带分页和筛选）
     * Get paginated video list with filters
     */
    public function getPaginatedVideos(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Video::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
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
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            $data['status'] = $data['status'] ?? 'draft';
            
            $tags = $data['tags'] ?? [];
            unset($data['tags']);
            
            $video = Video::create($data);

            if (!empty($tags)) {
                $tagIds = collect($tags)->map(function ($tag) {
                    if (is_numeric($tag)) {
                        return (int) $tag;
                    }
                    return \App\Models\Tag::firstOrCreate(['name' => $tag])->id;
                })->toArray();
                $video->tags()->sync($tagIds);
            }

            return $video;
        });
    }

    public function updateVideo(Video $video, array $data): Video
    {
        return DB::transaction(function () use ($video, $data) {
            if (isset($data['title']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $video->update($data);

            if (!empty($tags)) {
                $tagIds = collect($tags)->map(function ($tag) {
                    if (is_numeric($tag)) {
                        return (int) $tag;
                    }
                    return \App\Models\Tag::firstOrCreate(['name' => $tag])->id;
                })->toArray();
                $video->tags()->sync($tagIds);
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
            $video->tags()->detach();
            return $video->delete();
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