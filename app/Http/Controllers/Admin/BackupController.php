<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BackupController extends Controller
{
    private MockDataService $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): \Inertia\Response
    {
        // 使用模拟数据 - 先对接前端
        $backups = $this->mockDataService->getBackups();

        return Inertia::render('admin/Backup', [
            'backups' => $backups,
        ]);
    }

    public function create(Request $request)
    {
        // TODO: 先使用模拟逻辑，后续对接真实备份功能
        $request->validate([
            'type' => 'required|in:full,database,files,incremental',
            'note' => 'nullable|string',
        ]);

        // 模拟创建备份成功
        return back()->with('success', '备份创建成功');
    }

    public function download(string $id)
    {
        // TODO: 先使用模拟逻辑，后续对接真实下载功能
        return back()->with('success', '备份下载中...');
    }

    public function destroy(string $id)
    {
        // TODO: 先使用模拟逻辑，后续对接真实删除功能
        return back()->with('success', '备份已删除');
    }
}
