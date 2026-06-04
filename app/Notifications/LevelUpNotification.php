<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\UserLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

/**
 * LevelUpNotification - 等级升级通知
 *
 * 当用户积分达标自动升级时推送，前端可渲染升级提示。
 */
class LevelUpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly UserLevel $level,
        public readonly int $totalPoints,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'level_up',
            'title'   => "🎉 恭喜升级为 {$this->level->name}！",
            'message' => "你的积分已达到 {$this->totalPoints}，自动升级为 {$this->level->name}。",
            'level_name' => $this->level->name,
            'level_color' => $this->level->color,
            'level_icon'  => $this->level->icon,
            'total_points' => $this->totalPoints,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
