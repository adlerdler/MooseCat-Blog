<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $postUrl = url("/blog/{$this->comment->post_id}");
        
        return (new MailMessage)
            ->subject('新评论通知')
            ->line("用户 {$this->comment->author_name} 在文章上发表了评论：")
            ->line("\"{$this->comment->content}\"")
            ->action('查看文章', $postUrl)
            ->line('感谢使用 Archyx 博客系统！');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'author_name' => $this->comment->author_name,
            'content' => $this->comment->content,
            'type' => 'new_comment',
        ];
    }
}
