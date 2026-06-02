<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 超时时间：10分钟（全量备份可能较慢）
     */
    public $timeout = 600;

    /**
     * 失败重试次数
     */
    public $tries = 1;

    public function __construct(
        public int $backupId,
    ) {}

    public function handle(BackupService $backupService): void
    {
        $backup = Backup::find($this->backupId);

        if (! $backup || $backup->status !== 'pending') {
            return;
        }

        try {
            match ($backup->type) {
                'database'    => $backupService->executeDatabaseBackup($backup),
                'files'       => $backupService->executeFileBackup($backup),
                'incremental' => $backupService->executeIncrementalBackup($backup),
                default       => $backupService->executeFullBackup($backup),
            };

            $backupService->finalizeBackup($backup, 'completed');
        } catch (\Throwable $e) {
            $backupService->finalizeBackup($backup, 'failed', $e->getMessage());
        }
    }
}
