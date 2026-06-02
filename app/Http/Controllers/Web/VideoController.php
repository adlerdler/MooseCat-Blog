<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __construct(
        protected VisitService $visitService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['platform']);
        
        $videos = Video::query()
            ->when($filters['platform'] ?? null, function ($query, $platform) {
                $query->where('platform', $platform);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('videos.index', compact('videos', 'filters'));
    }

    public function show(Video $video): View
    {
        $this->visitService->trackModel($video, $request);
        return view('videos.show', compact('video'));
    }
}
