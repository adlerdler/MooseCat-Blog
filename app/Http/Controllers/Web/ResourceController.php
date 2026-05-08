<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Resource as ResourceModel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
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

        return view('resources.index', compact('resources', 'filters'));
    }

    public function show(ResourceModel $resource): View
    {
        $resource->load(['category', 'tags']);
        $resource->increment('views_count');
        return view('resources.show', compact('resource'));
    }
}
