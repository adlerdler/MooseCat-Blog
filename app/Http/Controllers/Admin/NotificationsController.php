<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Notifications Controller
 * 
 * Handles notification management operations.
 * Provides functionality for viewing, marking as read, and clearing notifications.
 */
class NotificationsController extends Controller
{
    protected $mockDataService;

    /**
     * Constructor
     * 
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display the notification list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $notifications = $this->mockDataService->getNotifications();
        
        return Inertia::render('admin/Notifications', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(string $id)
    {
        return back()->with('success', '通知已标记为已读');
    }

    /**
     * Clear all notifications
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        return back()->with('success', '通知已清空');
    }
}