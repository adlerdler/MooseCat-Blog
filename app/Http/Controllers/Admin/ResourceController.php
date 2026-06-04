<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use App\Services\ResourceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    protected ResourceService $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->middleware('permission:manage_resources');
        $this->resourceService = $resourceService;
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['category', 'search']);

        $resources = Resource::query()
            ->with(['category'])
            ->when($filters['category'] ?? null, fn($q, $cat) => $q->where('category_id', $cat))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'category_id' => $resource->category_id,
                    'category_name' => $resource->category?->name,
                    'title' => $resource->title,
                    'description' => $resource->description,
                    'format' => $resource->format,
                    'file_size' => $resource->file_size,
                    'image' => $resource->image,
                    'direct_link' => $resource->direct_link,
                    'drives' => $resource->drives ?? [],
                    'downloads_count' => $resource->downloads_count,
                    'likes_count' => $resource->likes_count,
                    'author_id' => $resource->author_id,
                    'created_at' => $resource->created_at?->format('Y-m-d'),
                ];
            });

        $categories = Category::all()->map(fn($c) => ['id' => $c->id, 'name' => $c->name]);
        $users = User::whereHas('roles')->get();

        return Inertia::render('admin/Resources', [
            'resources' => $resources,
            'categories' => $categories,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function store(StoreResourceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        $this->resourceService->createResource($data);

        return back()->with('success', '资源创建成功');
    }

    public function edit(Resource $resource): Response
    {
        $categories = Category::all()->map(fn($c) => ['id' => $c->id, 'name' => $c->name]);

        return Inertia::render('admin/Resources', [
            'resource' => [
                'id' => $resource->id,
                'category_id' => $resource->category_id,
                'title' => $resource->title,
                'description' => $resource->description,
                'format' => $resource->format ?? '',
                'file_size' => $resource->file_size ?? '',
                'image' => $resource->image ?? '',
                'direct_link' => $resource->direct_link ?? '',
                'drives' => $resource->drives ?? [],
            ],
            'categories' => $categories,
        ]);
    }

    public function update(UpdateResourceRequest $request, Resource $resource): RedirectResponse
    {
        $data = $request->validated();
        $this->resourceService->updateResource($resource, $data);

        return back()->with('success', '资源更新成功');
    }

    public function destroy(Resource $resource): RedirectResponse
    {
        $this->resourceService->deleteResource($resource);

        return back()->with('success', '资源删除成功');
    }
}