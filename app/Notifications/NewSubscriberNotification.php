<?php

namespace App\Notifications;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
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
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('新订阅者通知')
            ->line("新订阅者：{$this->subscriber->email}")
            ->line("订阅时间：{$this->subscriber->created_at->format('Y-m-d H:i:s')}")
            ->action('查看订阅者列表', url('/admin/subscribers'))
            ->line('感谢使用 Archyx 博客系统！');
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
