<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoService
{
    public function getPaginatedVideos(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Video::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? 'published', fn($q, $status) => $q->where('status', $status))
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getVideoBySlug(string $slug): ?Video
    {
        return Video::where('slug', $slug)->with(['category', 'tags'])->first();
    }

    public function getVideos(array $filters = []): Collection
    {
        return Video::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->latest('published_at')
            ->get();
    }

    public function createVideo(array $data): Video
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            $video = Video::create($data);
            if (isset($data['tags'])) {
                $video->tags()->sync($data['tags']);
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
            $video->update($data);
            if (isset($data['tags'])) {
                $video->tags()->sync($data['tags']);
            }
            return $video;
        });
    }

    public function deleteVideo(Video $video): bool
    {
        return DB::transaction(function () use ($video) {
            $video->tags()->detach();
            return $video->delete();
        });
    }

    public function incrementViews(Video $video): void
    {
        $video->increment('views_count');
    }
}
