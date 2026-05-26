<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

class RestoreController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $backups = $this->mockDataService->getBackups();
        
        return Inertia::render('admin/Restore', [
            'backups' => $backups,
        ]);
    }

    public function restore(string $id)
    {
        return back()->with('success', '数据已恢复');
    }
}