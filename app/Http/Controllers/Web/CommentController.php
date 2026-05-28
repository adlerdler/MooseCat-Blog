<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Events\CommentCreated;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService
    ) {}

    /**
     * Web 端提交评论
     */
    public function store(Request $request, Post $post): RedirectResponse
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

        event(new CommentCreated($comment));

        return back()->with('success', '评论提交成功，审核后将显示。');
    }
}
