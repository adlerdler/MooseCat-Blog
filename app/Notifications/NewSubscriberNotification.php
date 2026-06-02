<?php

namespace App\Notifications;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewSubscriberNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Subscriber $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function via(object $notifiable): array
    {
        // 仅写入站内 database 通知；邮件通过 MailService 在 Controller 中发送
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscriber_id' => $this->subscriber->id,
            'email' => $this->subscriber->email,
            'type' => 'new_subscriber',
        ];
    }
}
