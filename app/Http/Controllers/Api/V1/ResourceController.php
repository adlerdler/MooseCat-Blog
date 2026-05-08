<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ResourceResource;
use App\Models\Resource as ResourceModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResourceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['category', 'format', 'tag']);
        
        $resources = ResourceModel::query()
            ->with(['category', 'tags'])
            ->when($filters['category'] ?? null, function ($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($filters['format'] ?? null, function ($query, $format) {
                $query->where('format', $format);
            })
            ->when($filters['tag'] ?? null, function ($query, $tag) {
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('slug', $tag);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return ResourceResource::collection($resources);
    }

    public function show(ResourceModel $resource): ResourceResource
    {
        $resource->load(['category', 'tags']);
        $resource->increment('views_count');
        return new ResourceResource($resource);
    }
}
