<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupRestoreService
{
    /**
     * 恢复备份（安全：恢复前自动创建快照）
     */
    public function restore(int $backupId): array
    {
        $backup = Backup::find($backupId);

        if (! $backup || $backup->status !== 'completed') {
            return ['success' => false, 'message' => '备份记录不存在或未完成'];
        }

        $disk = $backup->disk;
        $filePath = $backup->filename;

        if (! Storage::disk($disk)->exists($filePath)) {
            return ['success' => false, 'message' => '备份文件不存在'];
        }

        try {
            // ① 恢复前快照（纯 PHP，无需 mysqldump）
            $snapshotId = $this->createPreRestoreSnapshot($backup);

            // ② 根据类型恢复
            match ($backup->type) {
                'database'    => $this->restoreDatabase($disk, $filePath),
                'files'       => $this->restoreFiles($disk, $filePath),
                'incremental' => $this->restoreIncremental($disk, $filePath),
                'full'        => $this->restoreFull($disk, $filePath),
            };

            // ③ 清缓存
            $this->clearCache();

            return [
                'success'     => true,
                'message'     => '恢复成功',
                'snapshot_id' => $snapshotId,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => '恢复失败：' . $e->getMessage()];
        }
    }

    // ─── 恢复前快照 ──────────────────────────────────────

    private function createPreRestoreSnapshot(Backup $backup): int
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "pre_restore_snapshot_{$timestamp}.sql.gz";
        $tmpPath   = storage_path("app/backup-temp/pre_restore_{$timestamp}.sql");

        File::ensureDirectoryExists(dirname($tmpPath));

        // 纯 PHP 导出（不依赖 mysqldump）
        $this->dumpDatabaseToFile($tmpPath);

        $gzContent = gzencode(file_get_contents($tmpPath), 9);
        Storage::disk('backups')->put($filename, $gzContent);
        unlink($tmpPath);

        $snapshot = Backup::create([
            'filename'     => $filename,
            'size'         => Storage::disk('backups')->size($filename),
            'type'         => 'database',
            'status'       => 'completed',
            'disk'         => 'backups',
            'note'         => "恢复前自动快照 (恢复自备份 #{$backup->id})",
            'is_scheduled' => false,
            'completed_at' => now(),
        ]);

        return $snapshot->id;
    }

    // ─── 各类型恢复 ──────────────────────────────────────

    private function restoreDatabase(string $disk, string $filePath): void
    {
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        $content = Storage::disk($disk)->get($filePath);
        if ($ext === 'gz') {
            $content = gzdecode($content);
        }

        // 纯 PHP 导入（不依赖 mysql 命令）
        $this->importSql($content);
    }

    private function restoreFiles(string $disk, string $filePath): void
    {
        $tmpPath = storage_path("app/backup-temp/restore_files.zip");
        File::ensureDirectoryExists(dirname($tmpPath));

        file_put_contents($tmpPath, Storage::disk($disk)->get($filePath));

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            unlink($tmpPath);
            throw new \RuntimeException('无法打开备份压缩包');
        }

        $zip->extractTo(base_path('public'));
        $zip->close();
        unlink($tmpPath);
    }

    private function restoreIncremental(string $disk, string $filePath): void
    {
        // 文件恢复
        $this->restoreFiles($disk, $filePath);

        // 数据库快照恢复
        $tmpPath = storage_path("app/backup-temp/incremental_restore.zip");
        File::ensureDirectoryExists(dirname($tmpPath));
        file_put_contents($tmpPath, Storage::disk($disk)->get($filePath));

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_starts_with($name, 'db_snapshot_') && str_ends_with($name, '.sql')) {
                    $sqlContent = $zip->getFromIndex($i);
                    $this->importSql($sqlContent);
                    break;
                }
            }
            $zip->close();
        }
        unlink($tmpPath);
    }

    private function restoreFull(string $disk, string $filePath): void
    {
        $tmpPath = storage_path("app/backup-temp/restore_full.zip");
        File::ensureDirectoryExists(dirname($tmpPath));
        file_put_contents($tmpPath, Storage::disk($disk)->get($filePath));

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            unlink($tmpPath);
            throw new \RuntimeException('无法打开全量备份包');
        }

        $extractDir = storage_path('app/backup-temp/restore_full_extracted');
        File::ensureDirectoryExists($extractDir);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $targetPath = $extractDir . '/' . $name;

            if (str_ends_with($name, '/')) {
                File::ensureDirectoryExists($targetPath);
            } else {
                File::ensureDirectoryExists(dirname($targetPath));
                file_put_contents($targetPath, $zip->getFromIndex($i));
            }
        }
        $zip->close();

        // 数据库恢复
        $sqlFiles = File::glob($extractDir . '/db-dumps/*.sql');
        if (! empty($sqlFiles)) {
            $this->importSqlFile($sqlFiles[0]);
        }

        // 文件恢复（排除 db-dumps 目录）
        $fileDirs = array_filter(File::directories($extractDir), fn($d) => basename($d) !== 'db-dumps');
        foreach ($fileDirs as $dir) {
            $dirName = basename($dir);
            $targetBase = match ($dirName) {
                'storage' => storage_path('app/public'),
                'uploads' => public_path('uploads'),
                default   => null,
            };
            if ($targetBase) {
                File::copyDirectory($dir, $targetBase);
            }
        }

        File::deleteDirectory($extractDir);
        unlink($tmpPath);
    }

    // ─── 纯 PHP 数据库操作（零外部命令依赖）───────────────

    /**
     * 导出当前数据库为 SQL 文件
     */
    private function dumpDatabaseToFile(string $outputPath): void
    {
        $handle = fopen($outputPath, 'w');
        fwrite($handle, "-- Pre-restore Snapshot\n");
        fwrite($handle, "-- Generated: " . now()->toIso8601String() . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET NAMES utf8mb4;\n\n");

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = reset($table);
            if ($tableName === 'migrations') {
                continue;
            }

            $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $create[0]->{'Create Table'};
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, "{$createSql};\n\n");

            DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function ($rows) use ($handle, $tableName) {
                if ($rows->isEmpty()) {
                    return;
                }

                $columns = array_keys((array) $rows->first());
                $colList = '`' . implode('`, `', $columns) . '`';

                fwrite($handle, "INSERT INTO `{$tableName}` ({$colList}) VALUES\n");

                $first = true;
                foreach ($rows as $row) {
                    $row = (array) $row;
                    $vals = [];
                    foreach ($columns as $col) {
                        $val = $row[$col] ?? null;
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = "'" . addslashes((string) $val) . "'";
                        }
                    }

                    if (! $first) {
                        fwrite($handle, ",\n");
                    }
                    $first = false;
                    fwrite($handle, '(' . implode(', ', $vals) . ')');
                }
                fwrite($handle, ";\n\n");
            });
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * 从 SQL 字符串导入数据库
     */
    private function importSql(string $sql): void
    {
        DB::unprepared($sql);
    }

    /**
     * 从 SQL 文件路径导入数据库
     */
    private function importSqlFile(string $filePath): void
    {
        if (! file_exists($filePath)) {
            throw new \RuntimeException('SQL 文件不存在: ' . $filePath);
        }

        DB::unprepared(file_get_contents($filePath));
    }

    // ─── 缓存清理 ────────────────────────────────────────

    private function clearCache(): void
    {
        Artisan::call('optimize:clear');
    }
}
