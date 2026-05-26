<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Backup Controller
 * 
 * Handles backup management operations.
 * Provides functionality for creating, downloading, and deleting backups.
 */
class BackupController extends Controller
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
     * Display the backup list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $backups = $this->mockDataService->getBackups();
        
        return Inertia::render('admin/Backup', [
            'backups' => $backups,
        ]);
    }

    /**
     * Create a new backup
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        return back()->with('success', '备份已创建');
    }

    /**
     * Download a backup file
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download(string $id)
    {
        return back();
    }

    /**
     * Remove the specified backup
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '备份已删除');
    }
}