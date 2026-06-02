<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Events\CommentCreated;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\CommentService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $commentService,
        protected SettingService $settingService,
    ) {}

    /**
     * Web 端提交评论
     * - AJAX 请求（X-Inertia / wantsJson）→ 返回 JSON，前端无需刷新即可显示
     * - 传统表单 POST → RedirectResponse
     */
    public function store(Request $request, Post $post): RedirectResponse|JsonResponse
    {
        // 检查评论功能是否启用
        $commentConfig = $this->settingService->getCommentConfig();
        if (!$commentConfig['enabled']) {
            if ($request->expectsJson() || $request->header('X-Inertia')) {
                return response()->json(['message' => '评论功能已关闭。'], 403);
            }
            abort(403, '评论功能已关闭。');
        }

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

        // AJAX 请求 → 直接返回新评论 JSON，前端无需刷新
        if ($request->expectsJson() || $request->header('X-Inertia')) {
            return response()->json([
                'message' => '评论提交成功。',
                'comment' => $comment->toArray(),
            ], 201);
        }

        return back()->with('success', '评论提交成功，审核后将显示。');
    }
}
