<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBackupRequest;
use App\Models\Backup;
use App\Jobs\CreateBackupJob;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {
        $this->middleware('permission:manage_backup');
    }

    /**
     * 备份列表页（前端自己做搜索/过滤/分页，后端返回全量格式化数据）
     */
    public function index(): Response
    {
        return Inertia::render('admin/Backup', [
            'backups' => $this->backupService->getAllBackups(),
            'stats'   => $this->backupService->getStats(),
        ]);
    }

    /**
     * 创建备份（异步：立即返回，后台 Job 执行）
     */
    public function create(StoreBackupRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            // 1. 创建 pending 状态的记录
            $backup = $this->backupService->createRecord(
                type:        $validated['type'],
                note:        $validated['note'] ?? null,
                isScheduled: false,
            );

            // 2. 分发异步 Job
            CreateBackupJob::dispatch($backup->id);

            return redirect()->route('admin.backup')->with(
                'success',
                "{$this->typeLabel($backup->type)}备份任务已创建，正在后台执行..."
            );
        } catch (\Exception $e) {
            return back()->with('error', '创建备份失败：' . $e->getMessage());
        }
    }

    /**
     * 下载备份文件
     */
    public function download(string $id)
    {
        $response = $this->backupService->download((int) $id);

        if (! $response) {
            return back()->with('error', '备份文件不存在或尚未完成');
        }

        // 记录下载操作日志（GET 请求不被中间件捕获）
        $backup = Backup::find((int) $id);
        if ($backup) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($backup)
                ->withProperties([
                    'filename'  => $backup->filename,
                    'size'      => $this->backupService->formatSize((int) $backup->size),
                    'type'      => $this->typeLabel($backup->type),
                ])
                ->inLog('backups')
                ->event('downloaded')
                ->log("下载{$this->typeLabel($backup->type)}备份: {$backup->filename}");
        }

        return $response;
    }

    /**
     * 删除备份
     */
    public function destroy(string $id): RedirectResponse
    {
        // 先查备份信息用于日志（删除后模型即不存在）
        $backup = Backup::find((int) $id);

        $success = $this->backupService->delete((int) $id);

        if ($backup) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($backup)
                ->withProperties([
                    'filename' => $backup->filename,
                    'size'     => $this->backupService->formatSize((int) $backup->size),
                    'type'     => $this->typeLabel($backup->type),
                ])
                ->inLog('backups')
                ->event('deleted')
                ->log("删除{$this->typeLabel($backup->type)}备份: {$backup->filename}");
        }

        return back()->with(
            $success ? 'success' : 'error',
            $success ? '备份已删除' : '备份不存在'
        );
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'full'        => '全量',
            'database'    => '数据库',
            'files'       => '文件',
            'incremental' => '增量',
            default       => '',
        };
    }
}
