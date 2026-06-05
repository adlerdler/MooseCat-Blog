<?php

use Illuminate\Support\Facades\Schedule;

// ─── 周报和摘要邮件调度 ────────────────────────────────

// 每周一 08:00 发送周报
Schedule::command('email:weekly-report')
    ->weeklyOn(1, '8:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/email-scheduler.log'));

// 每天 09:00 发送每日摘要
Schedule::command('email:digest daily')
    ->dailyAt('9:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/email-scheduler.log'));

// 每周一 09:00 发送每周摘要
Schedule::command('email:digest weekly')
    ->weeklyOn(1, '9:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/email-scheduler.log'));

// 每月 1 日 09:00 发送每月摘要
Schedule::command('email:digest monthly')
    ->monthlyOn(1, '9:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/email-scheduler.log'));

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
