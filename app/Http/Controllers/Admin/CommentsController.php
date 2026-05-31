<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CommentsController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(): Response
    {
        $comments = Comment::with(['post', 'user', 'parent'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'post_id' => $c->post_id,
                'post_title' => $c->post?->title,
                'parent_id' => $c->parent_id,
                'parent_name' => $c->parent?->name,
                'user_id' => $c->user_id,
                'user_name' => $c->user?->name ?? $c->name,
                'email' => $c->email,
                'body' => $c->body,
                'is_approved' => $c->is_approved,
                'created_at' => $c->created_at?->format('Y-m-d H:i'),
            ]);

        $posts = Post::orderBy('title')->get(['id', 'title']);

        return Inertia::render('admin/Comments', [
            'comments' => $comments,
            'posts' => $posts,
        ]);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $data = $request->validated();
        
        if (isset($data['is_approved'])) {
            $comment->update(['is_approved' => $data['is_approved']]);
        } else {
            $comment->update($data);
        }

        return back()->with('success', '评论状态已更新');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->commentService->deleteComment($comment);

        return back()->with('success', '评论已删除');
    }
}