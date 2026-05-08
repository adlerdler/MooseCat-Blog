<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService
    ) {}

    /**
     * 获取文章评论列表
     */
    public function index(Post $post): AnonymousResourceCollection
    {
        $comments = $post->comments()->with('user')->orderBy('created_at', 'desc')->paginate(20);
        
        return \App\Http\Resources\V1\CommentResource::collection($comments);
    }

    /**
     * 提交评论
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();
        $validated['user_id'] = $request->user()?->id;

        $comment = $this->commentService->createComment($post, $validated);

        return response()->json([
            'message' => 'Comment submitted successfully.',
            'data' => $comment,
        ], 201);
    }
}
