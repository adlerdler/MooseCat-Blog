<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class LogsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $logs = $this->mockDataService->getLogs();
        
        return Inertia::render('admin/Logs', [
            'logs' => $logs,
        ]);
    }

    public function clear()
    {
        return back()->with('success', '日志已清空');
    }
}