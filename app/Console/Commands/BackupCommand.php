<?php

namespace App\Console\Commands;

use App\Jobs\CreateBackupJob;
use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupCommand extends Command
{
    protected $signature = 'backup:run-custom
                            {type : 备份类型 (full|database|files|incremental)}
                            {--note= : 备份备注}';

    protected $description = '创建指定类型的备份（异步 Job 调度）';

    public function handle(BackupService $backupService): int
    {
        $type = $this->argument('type');
        $note = $this->option('note') ?: "定时调度 - {$type}备份";

        $this->info("创建 {$type} 备份记录...");

        $backup = $backupService->createRecord(
            type:        $type,
            note:        $note,
            isScheduled: true,
        );

        CreateBackupJob::dispatch($backup->id);

        $this->info("✅ 备份任务 #{$backup->id} 已入队，将在后台执行");
        return self::SUCCESS;
    }
}
