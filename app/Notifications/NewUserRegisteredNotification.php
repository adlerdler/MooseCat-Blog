<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    public User $newUser;

    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'name'    => $this->newUser->name,
            'email'   => $this->newUser->email,
            'type'    => 'new_user_registered',
            'title'   => '新用户注册',
            'message' => $this->newUser->name . ' (' . $this->newUser->email . ') 注册了账号',
        ];
    }
}
