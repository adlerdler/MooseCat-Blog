<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Restore Controller
 * 
 * Handles data restore operations.
 * Provides functionality for restoring data from backups.
 */
class RestoreController extends Controller
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
     * Display the restore page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $backups = $this->mockDataService->getBackups();
        
        return Inertia::render('admin/Restore', [
            'backups' => $backups,
        ]);
    }

    /**
     * Restore data from backup
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(string $id)
    {
        return back()->with('success', '数据已恢复');
    }
}