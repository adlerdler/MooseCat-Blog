<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Subscriber;

/**
 * SubscriberService - 订阅者管理服务类
 *
 * 提供订阅者的 CRUD 功能。
 * Provides subscriber CRUD functionality.
 */
class SubscriberService
{
    public function __construct() {}

    /**
     * 获取所有订阅者列表
     * Get all subscribers list.
     */
    public function getAll(): array
    {
        return Subscriber::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'email'         => $s->email,
                'name'          => $s->name,
                'source'        => $s->source,
                'is_active'     => $s->is_active,
                'subscribed_at' => $s->subscribed_at?->format('Y-m-d'),
                'created_at'    => $s->created_at?->format('Y-m-d'),
            ])->toArray();
    }

    /**
     * 创建订阅者
     * Create a new subscriber.
     */
    public function create(array $data): Subscriber
    {
        return Subscriber::create($data);
    }

    /**
     * 更新订阅者
     * Update a subscriber.
     */
    public function update(Subscriber $subscriber, array $data): Subscriber
    {
        $subscriber->update($data);
        return $subscriber;
    }

    /**
     * 删除订阅者
     * Delete a subscriber.
     */
    public function delete(Subscriber $subscriber): void
    {
        $subscriber->delete();
    }
}
