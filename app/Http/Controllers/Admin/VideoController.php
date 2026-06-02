<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VideoController extends Controller
{
    public function __construct(protected VideoService $videoService)
    {
        $this->middleware('permission:manage_videos');
    }

    public function index(): Response
    {
        $videosData = $this->videoService->getPaginatedVideos(100, request()->only('category', 'status'));
        $videos = collect($videosData->items())->map(function ($video) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'slug' => $video->slug,
                'description' => $video->description,
                'video_url' => $video->video_url,
                'video_id' => $video->video_id,
                'cover_image' => $video->cover_image,
                'duration' => $video->duration,
                'status' => $video->status,
                'category_id' => $video->category_id,
                'published_at' => $video->published_at,
                'tags' => $video->tags->pluck('name')->toArray(),
                'created_at' => $video->created_at,
                'updated_at' => $video->updated_at,
            ];
        })->toArray();
        $categories = Category::orderBy('sort_order')->get();

        return Inertia::render('admin/Videos', [
            'videos' => $videos,
            'categories' => $categories,
            'total' => $videosData->total(),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::orderBy('sort_order')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);

        return Inertia::render('admin/Videos', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function store(StoreVideoRequest $request)
    {
        $data = $request->validated();
        $this->videoService->createVideo($data);

        return redirect()->route('videos.index')->with('success', '视频已创建');
    }

    public function show(Video $video): Response
    {
        $video->load(['category', 'tags']);

        return Inertia::render('admin/Videos', [
            'video' => $video,
        ]);
    }

    public function edit(Video $video): Response
    {
        $video->load(['tags', 'category']);
        $categories = Category::orderBy('sort_order')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);

        $videoData = [
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'video_url' => $video->video_url,
            'video_id' => $video->video_id,
            'platform' => $video->platform,
            'cover_image' => $video->cover_image,
            'duration' => $video->duration,
            'category' => $video->category_id,
            'published_at' => $video->published_at ? date('Y.m.d', strtotime($video->published_at)) : date('Y.m.d'),
            'tags' => $video->tags->pluck('name')->join(', '),
            'status' => $video->status,
        ];

        return Inertia::render('admin/Videos', [
            'video' => $videoData,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $this->videoService->updateVideo($video, $request->validated());

        return redirect()->route('videos.index')->with('success', '视频已更新');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return redirect()->route('videos.index')->with('success', '视频已删除');
    }
}
