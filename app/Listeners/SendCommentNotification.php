<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentNotification;
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
        
        if (!$post) {
            return;
        }
        
        $author = User::find($post->author_id);
        
        if ($author) {
            $author->notify(new NewCommentNotification($comment));
        }
    }
}
