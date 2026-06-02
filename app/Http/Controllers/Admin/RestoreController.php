<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\BackupService;
use App\Services\BackupRestoreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RestoreController extends Controller
{
    public function __construct(
        protected BackupService $backupService,
        protected BackupRestoreService $restoreService,
    ) {
        $this->middleware('permission:manage_restore');
    }

    /**
     * 恢复页面：列出可恢复的备份
     */
    public function index(): Response
    {
        $backups = $this->backupService->getAllBackups();

        return Inertia::render('admin/Restore', [
            'backups' => $backups,
        ]);
    }

    /**
     * 执行恢复
     */
    public function restore(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'confirmed' => 'required|accepted',
        ], [
            'confirmed.accepted' => '请确认您理解恢复操作会覆盖现有数据',
        ]);

        $result = $this->restoreService->restore((int) $id);

        if ($result['success']) {
            $msg = '数据恢复成功。';
            if (isset($result['snapshot_id'])) {
                $msg .= " 恢复前快照已保存（备份 #{$result['snapshot_id']}）";
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', $result['message']);
    }
}
