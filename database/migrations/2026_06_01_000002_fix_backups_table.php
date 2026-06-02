<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            // 添加缺失的业务字段
            if (!Schema::hasColumn('backups', 'note')) {
                $table->string('note')->nullable()->after('type')->comment('备份备注');
            }
            if (!Schema::hasColumn('backups', 'schedule_time')) {
                $table->timestamp('schedule_time')->nullable()->after('is_scheduled')->comment('调度时间');
            }
            if (!Schema::hasColumn('backups', 'disk')) {
                $table->string('disk')->default('backups')->after('filename')->comment('存储磁盘');
            }
        });

        // MySQL: 修改 type enum 新增 incremental
        DB::statement("ALTER TABLE backups MODIFY COLUMN type ENUM('full','database','files','incremental') NOT NULL COMMENT '类型'");
    }

    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->dropColumn(['note', 'schedule_time', 'disk']);
        });

        DB::statement("ALTER TABLE backups MODIFY COLUMN type ENUM('full','database','files') NOT NULL COMMENT '类型'");
    }
};
