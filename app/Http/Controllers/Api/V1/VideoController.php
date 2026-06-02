<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\VideoResource;
use App\Models\Video;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VideoController extends Controller
{
    public function __construct(
        protected VisitService $visitService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['platform']);
        
        $videos = Video::query()
            ->when($filters['platform'] ?? null, function ($query, $platform) {
                $query->where('platform', $platform);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return VideoResource::collection($videos);
    }

    public function show(Video $video): VideoResource
    {
        $this->visitService->trackModel($video, $request);
        return new VideoResource($video);
    }
}
