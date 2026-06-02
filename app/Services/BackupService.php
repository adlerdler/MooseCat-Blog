<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Databases\MySql;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class BackupService
{
    private string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default_backup_disk', 'backups');
    }

    // ─── 创建备份（异步）──────────────────────────────────

    /**
     * 创建备份记录并加入队列（立即返回，后台执行）
     */
    public function createRecord(string $type, ?string $note, bool $isScheduled): Backup
    {
        return Backup::create([
            'filename'      => '',
            'size'          => 0,
            'type'          => $type,
            'status'        => 'pending',
            'disk'          => $this->disk,
            'note'          => $note,
            'is_scheduled'  => $isScheduled,
            'schedule_time' => $isScheduled ? now() : null,
        ]);
    }

    // ─── 备份执行（由 Job 调用）───────────────────────────

    public function executeFullBackup(Backup $backup): void
    {
        $backup->update(['status' => 'running', 'started_at' => now()]);

        Artisan::call('backup:run', ['--disable-notifications' => true]);

        // spatie 文件在 backups/Laravel/ 下
        $this->discoverSpatieFile($backup, 'zip');
    }

    public function executeDatabaseBackup(Backup $backup): void
    {
        $backup->update(['status' => 'running', 'started_at' => now()]);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "db_backup_{$timestamp}.sql";
        $tmpPath  = storage_path("app/backup-temp/{$filename}");
        $destPath = "{$filename}.gz";

        File::ensureDirectoryExists(dirname($tmpPath));

        // 纯 PHP 导出数据库（不依赖 mysqldump）
        $this->dumpDatabaseToFile($tmpPath);

        // Gzip 压缩
        $gzContent = gzencode(file_get_contents($tmpPath), 9);
        Storage::disk($this->disk)->put($destPath, $gzContent);
        unlink($tmpPath);

        $backup->update([
            'filename'     => $destPath,
            'size'         => Storage::disk($this->disk)->size($destPath),
            'disk'         => $this->disk,
        ]);
    }

    public function executeFileBackup(Backup $backup): void
    {
        $backup->update(['status' => 'running', 'started_at' => now()]);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "files_backup_{$timestamp}.zip";
        $tmpPath   = storage_path("app/backup-temp/{$filename}");

        File::ensureDirectoryExists(dirname($tmpPath));

        $zip = new ZipArchive();
        $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $this->addDirToZip($zip, public_path('uploads'), 'uploads');
        $this->addDirToZip($zip, storage_path('app/public'), 'storage');
        $zip->close();

        Storage::disk($this->disk)->put($filename, file_get_contents($tmpPath));
        unlink($tmpPath);

        $backup->update([
            'filename' => $filename,
            'size'     => Storage::disk($this->disk)->size($filename),
            'disk'     => $this->disk,
        ]);
    }

    public function executeIncrementalBackup(Backup $backup): void
    {
        $backup->update(['status' => 'running', 'started_at' => now()]);

        $lastFull = Backup::where('type', 'full')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();
        $since = $lastFull?->created_at ?? now()->subDay();

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "incremental_backup_{$timestamp}.zip";
        $tmpPath   = storage_path("app/backup-temp/{$filename}");

        File::ensureDirectoryExists(dirname($tmpPath));

        $zip = new ZipArchive();
        $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $this->addChangedFilesToZip($zip, public_path('uploads'), 'uploads', $since);
        $this->addChangedFilesToZip($zip, storage_path('app/public'), 'storage', $since);

        // 附加数据库快照
        $dbFilename = "db_snapshot_{$timestamp}.sql";
        $dbTmpPath  = storage_path("app/backup-temp/{$dbFilename}");
        MySql::create()
            ->setDbName(config('database.connections.mysql.database'))
            ->setUserName(config('database.connections.mysql.username'))
            ->setPassword(config('database.connections.mysql.password'))
            ->setHost(config('database.connections.mysql.host'))
            ->setPort(config('database.connections.mysql.port'))
            ->dumpToFile($dbTmpPath);
        $zip->addFile($dbTmpPath, $dbFilename);
        $zip->close();

        Storage::disk($this->disk)->put($filename, file_get_contents($tmpPath));
        unlink($tmpPath);
        @unlink($dbTmpPath);

        $backup->update([
            'filename' => $filename,
            'size'     => Storage::disk($this->disk)->size($filename),
            'disk'     => $this->disk,
        ]);
    }

    /**
     * 完成或失败时统一更新状态
     */
    public function finalizeBackup(Backup $backup, string $status, ?string $error = null): void
    {
        $data = [
            'status'       => $status,
            'completed_at' => now(),
        ];

        if ($error) {
            // 过滤非 UTF-8 字节，防止 JSON 序列化报错
            $data['error_message'] = mb_convert_encoding($error, 'UTF-8', 'UTF-8');
        }

        // 刷新文件大小
        if ($backup->filename && Storage::disk($backup->disk)->exists($backup->filename)) {
            $data['size'] = Storage::disk($backup->disk)->size($backup->filename);
        }

        $backup->update($data);
    }

    // ─── 列表 ────────────────────────────────────────────

    public function getBackups(string $search = '', string $type = '', int $perPage = 6): LengthAwarePaginator
    {
        $query = Backup::orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query->paginate($perPage);
    }

    /**
     * 获取所有备份（前端自己做过滤分页，需要全量 + camelCase 字段）
     */
    public function getAllBackups(): array
    {
        return Backup::orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Backup $b) => [
                'id'         => $b->id,
                'name'       => $b->filename ?: 'pending...',
                'filename'   => $b->filename,
                'type'       => $b->type,
                'size'       => $this->formatSize((int) $b->size),
                'status'     => $b->status,
                'note'       => $b->note ?? '',
                'createdAt'  => $b->created_at?->toIso8601String(),
                'disk'       => $b->disk,
                'error'      => $b->error_message,
            ])
            ->toArray();
    }

    public function getCompletedBackups(): array
    {
        return Backup::successful()
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getStats(): array
    {
        $totalSize  = Backup::successful()->sum('size');
        $lastBackup = Backup::successful()->orderBy('created_at', 'desc')->first();

        return [
            'total_backups'  => Backup::count(),
            'total_size'     => $this->formatSize((int) $totalSize),
            'total_size_raw' => (int) $totalSize,
            'last_backup_at' => $lastBackup?->created_at?->toIso8601String(),
            'by_type'        => [
                'full'        => Backup::where('type', 'full')->count(),
                'database'    => Backup::where('type', 'database')->count(),
                'files'       => Backup::where('type', 'files')->count(),
                'incremental' => Backup::where('type', 'incremental')->count(),
            ],
        ];
    }

    // ─── 下载 ────────────────────────────────────────────

    public function download(int $id): ?StreamedResponse
    {
        $backup = Backup::find($id);
        if (! $backup || ! Storage::disk($backup->disk)->exists($backup->filename)) {
            return null;
        }

        return Storage::disk($backup->disk)->download(
            $backup->filename,
            $backup->filename,
            ['Content-Type' => 'application/octet-stream']
        );
    }

    // ─── 删除 ────────────────────────────────────────────

    public function delete(int $id): bool
    {
        $backup = Backup::find($id);
        if (! $backup) {
            return false;
        }

        if (Storage::disk($backup->disk)->exists($backup->filename)) {
            Storage::disk($backup->disk)->delete($backup->filename);
        }

        $backup->delete();
        return true;
    }

    // ─── 工具 ────────────────────────────────────────────

    public function formatSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $k     = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i     = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    // ─── 内部辅助 ────────────────────────────────────────

    /**
     * 纯 PHP 方式导出数据库为 SQL 文件（不依赖 mysqldump）
     */
    private function dumpDatabaseToFile(string $outputPath): void
    {
        $handle = fopen($outputPath, 'w');
        fwrite($handle, "-- Laravel Database Backup\n");
        fwrite($handle, "-- Generated: " . now()->toIso8601String() . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET NAMES utf8mb4;\n\n");

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');

        foreach ($tables as $table) {
            $tableName = reset($table); // 第一个字段值就是表名

            // 跳过迁移记录表
            if ($tableName === 'migrations') {
                continue;
            }

            // DROP + CREATE TABLE
            $create = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSql = $create[0]->{'Create Table'};
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, "{$createSql};\n\n");

            // 分批导出数据
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

    private function discoverSpatieFile(Backup $backup, string $ext): void
    {
        $files    = Storage::disk($this->disk)->allFiles('Laravel');
        $matching = array_values(array_filter($files, fn($f) => str_ends_with($f, ".{$ext}")));
        rsort($matching);

        if (! empty($matching)) {
            $backup->update([
                'filename' => $matching[0],
                'disk'     => $this->disk,
            ]);
        }
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $prefix): void
    {
        if (! is_dir($dir)) return;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath     = $file->getRealPath();
            $relativePath = $prefix . '/' . substr($filePath, strlen($dir) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }

    private function addChangedFilesToZip(ZipArchive $zip, string $dir, string $prefix, $since): void
    {
        if (! is_dir($dir)) return;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->getMTime() >= $since->timestamp) {
                $filePath     = $file->getRealPath();
                $relativePath = $prefix . '/' . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
