<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationsController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
        $this->middleware('permission:manage_notifications');
    }

    /**
     * 通知列表页
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        try {
            $notifications = $this->notificationService->getAdminNotifications(
                user:    $user,
                page:    (int) $request->input('page', 1),
                perPage: (int) $request->input('per_page', 20),
            );
            $unreadCount = $this->notificationService->getUnreadCount($user);
            $pagination = [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ];
            $items = $notifications->items();
        } catch (\Exception $e) {
            // 表不存在等降级：返回空数据
            $items = [];
            $unreadCount = 0;
            $pagination = [
                'current_page' => 1,
                'last_page'    => 1,
                'per_page'     => 20,
                'total'        => 0,
            ];
        }

        return Inertia::render('admin/Notifications', [
            'notifications' => $items,
            'unreadCount'   => $unreadCount,
            'pagination'    => $pagination,
        ]);
    }

    /**
     * 管理员手动创建通知
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'type'    => 'required|string|in:info,warning,error,success',
            'link'    => 'nullable|string|max:500',
        ]);

        try {
            $this->notificationService->createAdminNotification(
                sender:  $request->user(),
                title:   $validated['title'],
                message: $validated['message'],
                type:    $validated['type'],
                link:    $validated['link'] ?? null,
            );

            return back()->with('success', '通知已创建');
        } catch (\Exception $e) {
            return back()->with('error', '通知创建失败：' . $e->getMessage());
        }
    }

    /**
     * 标记单条通知为已读
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        try {
            $success = $this->notificationService->markAsRead($request->user(), $id);
        } catch (\Exception $e) {
            return back()->with('error', '操作失败：' . $e->getMessage());
        }

        return back()->with(
            $success ? 'success' : 'error',
            $success ? '通知已标记为已读' : '通知不存在'
        );
    }

    /**
     * 全部标记为已读
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        try {
            $count = $this->notificationService->markAllAsRead($request->user());

            return back()->with('success', "已标记 {$count} 条通知为已读");
        } catch (\Exception $e) {
            return back()->with('error', '操作失败：' . $e->getMessage());
        }
    }

    /**
     * 删除单条通知
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        try {
            $success = $this->notificationService->delete($request->user(), $id);
        } catch (\Exception $e) {
            return back()->with('error', '操作失败：' . $e->getMessage());
        }

        return back()->with(
            $success ? 'success' : 'error',
            $success ? '通知已删除' : '通知不存在'
        );
    }

    /**
     * 清空所有通知
     */
    public function clear(Request $request): RedirectResponse
    {
        try {
            $count = $this->notificationService->clear($request->user());

            return back()->with('success', "已清空 {$count} 条通知");
        } catch (\Exception $e) {
            return back()->with('error', '操作失败：' . $e->getMessage());
        }
    }
}
