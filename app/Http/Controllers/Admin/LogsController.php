<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LogsController extends Controller
{
    private MockDataService $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(Request $request): Response
    {
        // 使用模拟数据 - 先对接前端
        $logs = $this->mockDataService->getLogs();

        return Inertia::render('admin/Logs', [
            'logs' => $logs,
        ]);
    }

    public function clear()
    {
        // TODO: 先使用模拟逻辑，后续对接真实功能
        return back()->with('success', '日志已清空');
    }

    public function destroy(string $id)
    {
        // TODO: 先使用模拟逻辑，后续对接真实功能
        return back()->with('success', '日志已删除');
    }
}