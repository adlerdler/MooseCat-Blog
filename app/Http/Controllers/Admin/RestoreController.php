<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RestoreController extends Controller
{
    private MockDataService $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->middleware('permission:manage_restore');
        $this->mockDataService = $mockDataService;
    }

    public function index(): \Inertia\Response
    {
        // 使用模拟数据 - 先对接前端
        $backups = $this->mockDataService->getBackups();

        return Inertia::render('admin/Restore', [
            'backups' => $backups,
        ]);
    }

    public function restore(string $id)
    {
        // TODO: 先使用模拟逻辑，后续对接真实恢复功能
        return back()->with('success', '数据恢复中...');
    }
}
