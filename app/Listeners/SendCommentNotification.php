<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCommentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;
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

            app(MailService::class)->send($author->email, $subject, $htmlBody);
        }
    }
}
