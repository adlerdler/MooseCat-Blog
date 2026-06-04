<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Jobs\SendEmailJob;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class SendCommentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * 去重锁过期时间（秒）。
     * 同一评论的通知在锁过期前不会重复发送。
     */
    private const DEDUP_TTL = 60;

    public function __construct()
    {
    }

    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;

        // ─── 去重：确保同一评论的通知只发送一次 ────
        $lockKey = "comment_notified:{$comment->id}";
        if (Cache::has($lockKey)) {
            return;
        }
        Cache::put($lockKey, true, self::DEDUP_TTL);

        $post = Post::find($comment->post_id);
        
        if (! $post) {
            return;
        }
        
        $author = User::find($post->author_id);
        
        if (! $author) {
            return;
        }

        // 站内 database 通知
        $author->notify(new NewCommentNotification($comment));

        // 邮件通知（通过 MailService 直连 SMTP）
        if ($author->email && filter_var($author->email, FILTER_VALIDATE_EMAIL)) {
            $postUrl = rtrim(config('app.url', ''), '/') . '/blog/' . ($post->slug ?? $post->id);
            $subject = "新评论通知 | {$post->title}";

            $htmlBody = view('emails.notification', [
                'title'   => '新评论通知',
                'message' => "用户 <strong>{$comment->name}</strong> 在你的文章 <strong>{$post->title}</strong> 上发表了新评论。",
                'detail'  => e($comment->body),
                'actionUrl'  => $postUrl,
                'actionText' => '查看文章',
                'brandName'  => config('app.name', 'Archyx'),
                'timestamp'  => now()->format('Y-m-d H:i'),
            ])->render();

            dispatch(new SendEmailJob($author->email, $subject, $htmlBody))->afterResponse();
        }
    }
}
