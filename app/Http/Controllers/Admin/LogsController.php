<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LogsController extends Controller
{
    public function __construct(
        protected LogService $logService,
    ) {
        $this->middleware('permission:manage_logs');
    }

    /**
     * Display the logs page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('admin/Logs', [
            'logs' => $this->logService->getAll(),
        ]);
    }

    /**
     * Clear all logs.
     */
    public function clear(): \Illuminate\Http\RedirectResponse
    {
        $this->logService->clear();

        return back()->with('success', '日志已清空');
    }

    /**
     * Delete a specific log entry.
     */
    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        $this->logService->delete($id);

        return back()->with('success', '日志已删除');
    }
}
