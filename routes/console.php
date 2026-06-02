<?php

use Illuminate\Support\Facades\Schedule;

// ─── 备份调度 ──────────────────────────────────────────

// 每天 03:00 数据库备份
Schedule::command('backup:run-custom database --note="定时数据库备份"')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup-scheduler.log'));

// 每天 06:00 增量备份
Schedule::command('backup:run-custom incremental --note="定时增量备份"')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup-scheduler.log'));

// 每周日 02:00 全量备份
Schedule::command('backup:run-custom full --note="定时全量备份"')
    ->weeklyOn(0, '2:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup-scheduler.log'));

// 每周日 04:00 清理过期备份
Schedule::command('backup:clean')
    ->weeklyOn(0, '4:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup-scheduler.log'));
