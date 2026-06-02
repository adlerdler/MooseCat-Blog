<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        // 仅写入站内 database 通知；邮件通过 MailService 在 Listener 中发送
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id'  => $this->comment->id,
            'post_id'     => $this->comment->post_id,
            'author_name' => $this->comment->name,
            'content'     => $this->comment->body,
            'type'        => 'new_comment',
        ];
    }
}
