<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class BackupController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $backups = $this->mockDataService->getBackups();
        
        return Inertia::render('admin/Backup', [
            'backups' => $backups,
        ]);
    }

    public function create()
    {
        return back()->with('success', '备份已创建');
    }

    public function download(string $id)
    {
        return back();
    }

    public function destroy(string $id)
    {
        return back()->with('success', '备份已删除');
    }
}