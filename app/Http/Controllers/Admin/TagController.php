<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
        $this->middleware('permission:manage_tags');
    }

    public function index(): Response
    {
        $tags = Tag::withCount(['posts', 'projects', 'resources'])
            ->orderBy('name')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'posts_count' => $t->posts_count,
                'projects_count' => $t->projects_count,
                'resources_count' => $t->resources_count,
                'created_at' => $t->created_at?->format('Y-m-d'),
            ]);

        return Inertia::render('admin/Tags', [
            'tags' => $tags,
        ]);
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->tagService->createTag($data);

        return back()->with('success', '标签已创建');
    }

    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $data = $request->validated();
        $this->tagService->updateTag($tag, $data);

        return back()->with('success', '标签已更新');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->tagService->deleteTag($tag);

        return back()->with('success', '标签已删除');
    }
}