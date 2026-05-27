<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Resource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceService
{
    public function getPaginatedResources(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Resource::query()
            ->with(['category'])
            ->when($filters['category'] ?? null, fn($q, $slug) => $q->whereHas('category', fn($q) => $q->where('slug', $slug)))
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function getResourceBySlug(string $slug): ?Resource
    {
        return Resource::where('slug', $slug)->with(['category'])->first();
    }

    public function getResources(array $filters = []): Collection
    {
        return Resource::query()
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->latest('created_at')
            ->get();
    }

    public function createResource(array $data): Resource
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
            return Resource::create($data);
        });
    }

    public function updateResource(Resource $resource, array $data): Resource
    {
        return DB::transaction(function () use ($resource, $data) {
            if (isset($data['title']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }
            $resource->update($data);
            return $resource;
        });
    }

    public function deleteResource(Resource $resource): bool
    {
        return DB::transaction(function () use ($resource) {
            return $resource->delete();
        });
    }

    public function incrementDownloads(Resource $resource): void
    {
        $resource->increment('downloads_count');
    }
}
