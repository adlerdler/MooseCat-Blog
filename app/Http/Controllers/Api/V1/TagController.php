<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tags = Tag::orderBy('name')->get();
        return TagResource::collection($tags);
    }

    public function show(Tag $tag): TagResource
    {
        $tag->load(['posts', 'projects', 'resources']);
        return new TagResource($tag);
    }
}
