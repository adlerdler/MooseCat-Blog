<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class NotificationsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $notifications = $this->mockDataService->getNotifications();
        
        return Inertia::render('admin/Notifications', [
            'notifications' => $notifications,
            'unreadCount' => count(array_filter($notifications, fn($n) => !$n['is_read'])),
        ]);
    }

    public function markAsRead(string $id)
    {
        return back()->with('success', '通知已标记为已读');
    }

    public function markAllAsRead()
    {
        return back()->with('success', '所有通知已标记为已读');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '通知已删除');
    }

    public function clear()
    {
        return back()->with('success', '通知已清空');
    }
}