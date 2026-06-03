<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
    ) {}

    /**
     * Web 端文章列表页
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['category', 'tag', 'search']);
        $posts = $this->postService->getPaginatedPosts(12, $filters);

        return view('posts.index', compact('posts', 'filters'));
    }

    /**
     * Web 端文章详情页
     */
    public function show(Post $post, Request $request): View
    {
        $post->load(['author', 'category', 'tags']);

        return view('posts.show', compact('post'));
    }
}
