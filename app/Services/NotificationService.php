<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * 通知类型映射：Laravel data.type → 前端 type (info/warning/error/success)
     */
    private const TYPE_MAP = [
        'new_comment'                => 'info',
        'new_user'                   => 'info',
        'level_up'                   => 'success',
        'system_info'                => 'info',
        'system_warning'             => 'warning',
        'system_error'               => 'error',
        'system_success'             => 'success',
        'info'                       => 'info',
        'warning'                    => 'warning',
        'error'                      => 'error',
        'success'                    => 'success',
    ];

    /**
     * 获取管理后台通知分页列表
     */
    public function getAdminNotifications(User $user, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn ($notification) => $this->formatNotification($notification));
    }

    /**
     * 获取通知铃铛的最新未读通知（已读的不显示在铃铛下拉中）
     */
    public function getBellNotifications(User $user, int $limit = 5): Collection
    {
        return $user->unreadNotifications()
            ->take($limit)
            ->get()
            ->map(fn ($notification) => $this->formatNotification($notification));
    }

    /**
     * 获取未读通知数量
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * 标记单条通知为已读
     */
    public function markAsRead(User $user, string $id): bool
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * 标记所有通知为已读
     */
    public function markAllAsRead(User $user): int
    {
        $unreadIds = $user->unreadNotifications()->pluck('id');
        $user->unreadNotifications()->update(['read_at' => now()]);

        return $unreadIds->count();
    }

    /**
     * 删除单条通知
     */
    public function delete(User $user, string $id): bool
    {
        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return false;
        }

        $notification->delete();
        return true;
    }

    /**
     * 清空所有通知
     */
    public function clear(User $user): int
    {
        $count = $user->notifications()->count();
        $user->notifications()->delete();

        return $count;
    }

    /**
     * 管理员创建并发送系统通知（发送给自己/admin用户）
     */
    public function createAdminNotification(
        User $sender,
        string $title,
        string $message,
        string $type = 'info',
        ?string $link = null,
    ): void {
        // 目前发送给当前管理员自身（后续可扩展为发送给所有管理员）
        $sender->notify(new SystemNotification(
            title:   $title,
            message: $message,
            type:    $type,
            link:    $link,
        ));
    }

    /**
     * 将 Laravel DatabaseNotification 格式化为前端期望的结构
     */
    public function formatNotification(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];

        // 从通知类的 FQCN 提取简短类型名
        $classType = $this->extractTypeFromClass($notification->type);

        // data 中可能带 type 字段，也可能不带
        $rawType = $data['type'] ?? $classType;

        return [
            'id'         => $notification->id,
            'title'      => $data['title'] ?? $this->defaultTitle($rawType),
            'message'    => $data['message'] ?? ($data['content'] ?? ''),
            'type'       => self::TYPE_MAP[$rawType] ?? 'info',
            'link'       => $data['link'] ?? $this->defaultLink($data, $rawType),
            'read'       => $notification->read_at !== null,
            'created_at' => $notification->created_at?->toIso8601String(),
            'read_at'    => $notification->read_at?->toIso8601String(),
        ];
    }

    /**
     * 从通知类 FQCN 提取简短类型标识
     * e.g. App\Notifications\NewCommentNotification → new_comment
     */
    private function extractTypeFromClass(string $fqcn): string
    {
        $basename = class_basename($fqcn);
        // NewCommentNotification → new_comment
        $snake = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $basename));
        // Remove _notification suffix
        return str_replace('_notification', '', $snake);
    }

    /**
     * 根据类型生成默认标题
     */
    private function defaultTitle(string $type): string
    {
        return match ($type) {
            'new_comment'  => '新评论通知',
            'new_user'     => '新用户注册',
            'system_info'  => '系统通知',
            'system_warning' => '系统警告',
            'system_error'  => '系统错误',
            'system_success' => '操作成功',
            default        => '系统通知',
        };
    }

    /**
     * 根据 data 内容生成默认跳转链接
     */
    private function defaultLink(array $data, string $type): ?string
    {
        return match ($type) {
            'new_comment' => isset($data['post_id']) ? '/blog/' . $data['post_id'] : null,
            'new_user'    => '/admin/users',
            default       => null,
        };
    }
}
