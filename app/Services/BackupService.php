<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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

    /**
     * 全量备份 = 数据库完整导出 + 上传文件 + 配置文件 + .env
     * 不打包整个项目代码（代码由 Git 版本控制保护）
     */
    public function executeFullBackup(Backup $backup): void
    {
        // 绕过 PHP 30s 限制（sync 队列下 Job::$timeout 不生效）
        set_time_limit(0);

        $backup->update(['status' => 'running', 'started_at' => now()]);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "full_backup_{$timestamp}.zip";
        $tmpPath   = storage_path("app/backup-temp/{$filename}");

        File::ensureDirectoryExists(dirname($tmpPath));

        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('无法创建备份 ZIP 文件');
        }

        // 1. 导出数据库（纯 PHP，不依赖 mysqldump）
        $dbFilename = "database_full_{$timestamp}.sql";
        $dbTmpPath  = storage_path("app/backup-temp/{$dbFilename}");
        $this->dumpDatabaseToFile($dbTmpPath);
        $zip->addFile($dbTmpPath, $dbFilename);

        // 2. 上传文件（用户数据资产）
        if (is_dir(public_path('uploads'))) {
            $this->addDirToZip($zip, public_path('uploads'), 'uploads');
        }
        if (is_dir(storage_path('app/public'))) {
            $this->addDirToZip($zip, storage_path('app/public'), 'storage');
        }

        // 3. 配置文件
        if (is_dir(config_path())) {
            $this->addDirToZip($zip, config_path(), 'config');
        }
        // .env 环境变量
        if (file_exists(base_path('.env'))) {
            $zip->addFile(base_path('.env'), '.env');
        }

        $zip->close();

        // 4. 存入备份磁盘
        Storage::disk($this->disk)->put($filename, file_get_contents($tmpPath));
        unlink($tmpPath);
        @unlink($dbTmpPath);

        // 5. 更新记录
        $backup->update([
            'filename' => $filename,
            'size'     => Storage::disk($this->disk)->size($filename),
            'disk'     => $this->disk,
        ]);
    }

    public function executeDatabaseBackup(Backup $backup): void
    {
        set_time_limit(0);

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
        set_time_limit(0);

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
        set_time_limit(0);

        $backup->update(['status' => 'running', 'started_at' => now()]);

        $lastFull = Backup::where('type', 'full')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();
        // 有全量基线 → 只打包基线之后的文件；没有基线 → 打包所有文件
        $since = $lastFull?->created_at;

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "incremental_backup_{$timestamp}.zip";
        $tmpPath   = storage_path("app/backup-temp/{$filename}");

        File::ensureDirectoryExists(dirname($tmpPath));

        $zip = new ZipArchive();
        $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $this->addChangedFilesToZip($zip, public_path('uploads'), 'uploads', $since);
        $this->addChangedFilesToZip($zip, storage_path('app/public'), 'storage', $since);

        // 附加数据库快照（纯 PHP 导出，不依赖 mysqldump）
        $dbFilename = "db_snapshot_{$timestamp}.sql";
        $dbTmpPath  = storage_path("app/backup-temp/{$dbFilename}");
        $this->dumpDatabaseToFile($dbTmpPath);
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
        // 刷新备份记录以获取 execute 方法中写入的最新 filename
        $backup->refresh();

        // 如果标记为完成但实际没有文件，改为失败
        if ($status === 'completed' && (empty($backup->filename) || ! Storage::disk($backup->disk)->exists($backup->filename))) {
            $status = 'failed';
            $error  = $error ?: '备份文件未生成或已丢失（filename 为空或文件不存在）';
        }

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

        // 每条备份只记一条操作日志
        $typeLabel = $this->typeLabel($backup->type);
        $sizeStr   = $this->formatSize((int) ($data['size'] ?? $backup->size));
        if ($status === 'completed') {
            $desc = "{$typeLabel}备份完成: {$backup->filename} ({$sizeStr})";
            $event = 'backup_completed';
        } else {
            $desc = "{$typeLabel}备份失败: " . ($data['error_message'] ?? $error ?? '未知错误');
            $event = 'backup_failed';
        }

        activity()
            ->causedBy(Auth::user())
            ->performedOn($backup)
            ->withProperties([
                'type'     => $backup->type,
                'filename' => $backup->filename,
                'size'     => $sizeStr,
                'error'    => $data['error_message'] ?? null,
            ])
            ->inLog('backups')
            ->event($event)
            ->log($desc);
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

    // ─── 预览 ────────────────────────────────────────────

    /**
     * 读取备份 ZIP 真实内容，返回结构化预览数据
     */
    public function preview(int $backupId): ?array
    {
        $backup = Backup::find($backupId);
        if (! $backup || $backup->status !== 'completed') {
            return null;
        }

        $disk = $backup->disk;
        $filePath = $backup->filename;
        if (! Storage::disk($disk)->exists($filePath)) {
            return null;
        }

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        // 非 ZIP 格式（纯 SQL / SQL.gz）→ 只解析 SQL
        if (! in_array(strtolower($ext), ['zip'])) {
            $content = Storage::disk($disk)->get($filePath);
            if ($ext === 'gz') {
                $content = gzdecode($content);
            }
            $sqlInfo = $this->parseSqlSummary($content);
            return [
                'tables'       => $sqlInfo['tables'],
                'totalRecords' => $sqlInfo['totalRecords'],
                'files'        => 0,
                'images'       => 0,
                'totalFiles'   => 0,
            ];
        }

        // ZIP 格式 → 逐项分析
        $tmpPath = storage_path("app/backup-temp/preview_{$backupId}.zip");
        File::ensureDirectoryExists(dirname($tmpPath));
        file_put_contents($tmpPath, Storage::disk($disk)->get($filePath));

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            @unlink($tmpPath);
            return ['tables' => [], 'totalRecords' => 0, 'files' => 0, 'images' => 0, 'totalFiles' => 0];
        }

        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
        $tables = [];
        $totalRecords = 0;
        $files = 0;
        $images = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_ends_with($name, '/')) {
                continue; // 跳过目录
            }

            $entryExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if ($entryExt === 'sql') {
                // 解析 SQL 文件内容
                $sqlContent = $zip->getFromIndex($i);
                $sqlInfo = $this->parseSqlSummary($sqlContent);
                $tables = array_merge($tables, $sqlInfo['tables']);
                $totalRecords += $sqlInfo['totalRecords'];
            } elseif (in_array($entryExt, $imageExts)) {
                $images++;
            } else {
                $files++;
            }
        }

        $zip->close();
        @unlink($tmpPath);

        $tables = array_values(array_unique($tables));

        return [
            'tables'       => $tables,
            'totalRecords' => $totalRecords,
            'files'        => $files,
            'images'       => $images,
            'totalFiles'   => $files + $images + (count($tables) > 0 ? 1 : 0),
        ];
    }

    /**
     * 解析 SQL 文件摘要：表名列表 + INSERT 行数
     */
    private function parseSqlSummary(string $sql): array
    {
        $tables = [];
        $recordCount = 0;

        // 提取表名：匹配 DROP TABLE IF EXISTS `xxx` 或 CREATE TABLE `xxx`
        if (preg_match_all('/DROP\s+TABLE\s+IF\s+EXISTS\s+`([^`]+)`/i', $sql, $matches)) {
            $tables = $matches[1];
        }

        // 统计 INSERT INTO 中的记录行数（每条 INSERT VALUES 后面括号数）
        if (preg_match_all('/INSERT\s+INTO\s+`[^`]+`\s+.*?VALUES\s+(.+?);/is', $sql, $inserts)) {
            foreach ($inserts[1] as $valuesBlock) {
                // 每条 VALUES 后有多组 (...) ，每对 () 是一条记录
                preg_match_all('/\([^)]*\)/', $valuesBlock, $rows);
                $recordCount += count($rows[0]);
            }
        }

        return [
            'tables'       => array_values(array_unique($tables)),
            'totalRecords' => $recordCount,
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

    public function typeLabel(string $type): string
    {
        return match ($type) {
            'full'        => '全量',
            'database'    => '数据库',
            'files'       => '文件',
            'incremental' => '增量',
            default       => $type,
        };
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

    /**
     * 递归添加目录到 ZIP，支持排除列表
     */
    private function addDirToZipWithExclude(ZipArchive $zip, string $dir, string $prefix, array $excludeDirs): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $basePath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();

            // 检查是否在排除目录中
            $relativeDir = substr(dirname($filePath), strlen($basePath));
            $skip = false;
            foreach ($excludeDirs as $exclude) {
                $exclude = str_replace('/', DIRECTORY_SEPARATOR, $exclude);
                $exclude = ltrim($exclude, DIRECTORY_SEPARATOR);
                if ($relativeDir === $exclude || str_starts_with($relativeDir, $exclude . DIRECTORY_SEPARATOR)) {
                    $skip = true;
                    break;
                }
            }

            if (! $skip) {
                $relativePath = substr($filePath, strlen($basePath));
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $zip->addFile($filePath, $relativePath);
            }
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
            // $since 为 null 表示没有全量基线，打包所有文件
            if ($since === null || $file->getMTime() >= $since->timestamp) {
                $filePath     = $file->getRealPath();
                $relativePath = $prefix . '/' . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
