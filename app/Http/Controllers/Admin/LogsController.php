<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Logs Controller
 * 
 * Handles system logs management.
 * Provides functionality for viewing and clearing system operation logs.
 */
class LogsController extends Controller
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
     * Display the system logs
     * 
     * @return Response
     */
    public function index(): Response
    {
        $logs = $this->mockDataService->getLogs();
        
        return Inertia::render('admin/Logs', [
            'logs' => $logs,
        ]);
    }

    /**
     * Clear all system logs
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        return back()->with('success', '日志已清空');
    }
}