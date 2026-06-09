<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Resource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * ResourceService - 资源服务类
 * 
 * 提供资源下载的管理功能，包括资源列表、创建、更新、删除和下载统计。
 * Provides resource management functionality, including resource listing, creation, 
 * update, deletion and download statistics.
 */
class ResourceService
{
    /**
     * 获取资源列表（带分页和筛选）
     * Get paginated resource list with filters
     */
    public function getPaginatedResources(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Resource::query()
            ->with(['category'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->latest('created_at')
            ->paginate($perPage);
    }



    /**
     * 获取所有资源
     * Get all resources
     */
    public function getResources(array $filters = []): Collection
    {
        return Resource::query()
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->latest('created_at')
            ->get();
    }

    /**
     * 创建资源
     * Create resource
     */
    public function createResource(array $data): Resource
    {
        return DB::transaction(function () use ($data) {
            return Resource::create($data);
        });
    }

    /**
     * 更新资源
     * Update resource
     */
    public function updateResource(Resource $resource, array $data): Resource
    {
        return DB::transaction(function () use ($resource, $data) {
            $resource->update($data);
            return $resource;
        });
    }

    /**
     * 删除资源
     * Delete resource
     */
    public function deleteResource(Resource $resource): bool
    {
        return DB::transaction(function () use ($resource) {
            return $resource->delete();
        });
    }

    /**
     * 增加下载次数
     * Increment download count
     */
    public function incrementDownloads(Resource $resource): void
    {
        $resource->increment('downloads_count');
    }
}