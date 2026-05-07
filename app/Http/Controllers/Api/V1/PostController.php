<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {}

    /**
     * 获取文章列表
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['category', 'tag', 'status']);
        $posts = $this->postService->getPaginatedPosts(15, $filters);

        return PostResource::collection($posts);
    }

    /**
     * 获取文章详情
     */
    public function show(Post $post): PostResource
    {
        $post->load(['author', 'category', 'tags']);
        $this->postService->incrementViews($post);

        return new PostResource($post);
    }
}
