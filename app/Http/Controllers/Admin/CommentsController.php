<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommentRequest;
use App\Jobs\SendEmailJob;
use App\Models\AuthorProfile;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentsController extends Controller
{
    protected CommentService $commentService;
    protected SettingService $settingService;

    public function __construct(CommentService $commentService, SettingService $settingService)
    {
        $this->commentService = $commentService;
        $this->settingService = $settingService;
        $this->middleware('permission:manage_comments');
    }

    public function index(): Response
    {
        $comments = Comment::with(['post', 'user', 'parent', 'children'])
            ->whereNull('parent_id')  // 只取顶层评论，子评论在 replies 中
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
                'name' => $c->name,
                'email' => $c->email,
                'body' => $c->body,
                'is_approved' => $c->is_approved,
                'created_at' => $c->created_at?->format('Y-m-d H:i'),
                'replies' => $c->children->map(fn($r) => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'body' => $r->body,
                    'is_admin' => $r->is_admin,
                    'is_approved' => $r->is_approved,
                    'created_at' => $r->created_at?->format('Y-m-d H:i'),
                ])->values(),
            ]);

        $posts = Post::orderBy('title')->get(['id', 'title']);

        return Inertia::render('admin/Comments', [
            'comments' => $comments,
            'posts' => $posts,
        ]);
    }

    /**
     * 管理员回复评论
     */
    public function reply(Request $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        // 获取管理员的作者笔名
        $penName = $this->resolvePenName($request);

        $reply = $this->commentService->createAdminReply($comment, [
            'body' => $validated['body'],
            'user_id' => $request->user()?->id,
            'name' => $penName,
            'email' => $request->user()?->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // 如果原评论者有邮箱，通过 Symfony Mailer 直接发送通知
        if ($comment->email && filter_var($comment->email, FILTER_VALIDATE_EMAIL)) {
            $this->sendReplyNotification($request, $comment, $reply, $penName);
        }

        return back()->with('success', '回复已发送');
    }

    /**
     * 解析管理员的作者笔名
     * 优先使用 AuthorProfile.display_name → 用户 name → 'Admin'
     */
    private function resolvePenName(Request $request): string
    {
        $user = $request->user();
        if (! $user) {
            return 'Admin';
        }

        $profile = AuthorProfile::where('user_id', $user->id)->first();
        if ($profile?->display_name) {
            return $profile->display_name;
        }

        return $user->name ?: 'Admin';
    }

    /**
     * 发送回复通知邮件（通过 MailService）
     */
    private function sendReplyNotification(Request $request, Comment $comment, Comment $reply, string $penName): void
    {
        $siteConfig = $this->settingService->getSiteConfig();
        $brandName = $siteConfig['name'] ?? config('app.name', 'Arkhyx');
        $settings = $this->settingService->getAll();
        $logo = isset($settings['logo']) && $settings['logo'] ? url($settings['logo']) : '';
        $postTitle = $comment->post?->title ?? '文章';
        $postUrl = rtrim(config('app.url', ''), '/') . '/blog/' . ($comment->post?->slug ?? $comment->post_id);
        $siteUrl = config('app.url', '');
        $timestamp = now()->format('Y-m-d H:i');
        $commentBody = mb_strlen($comment->body) > 200
            ? mb_substr($comment->body, 0, 200) . '...'
            : $comment->body;

        $htmlBody = view('emails.comment-replied', [
            'commenterName' => $comment->name ?? 'Anonymous',
            'commentBody'   => $commentBody,
            'replyBody'     => $reply->body,
            'postTitle'     => $postTitle,
            'postUrl'       => $postUrl,
            'brandName'     => $brandName,
            'logo'          => $logo,
            'penName'       => $penName,
            'timestamp'     => $timestamp,
            'siteUrl'       => $siteUrl,
        ])->render();

        dispatch(new SendEmailJob(
            $comment->email,
            "{$penName} 回复了你的评论 | {$postTitle}",
            $htmlBody
        ))->afterResponse();
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