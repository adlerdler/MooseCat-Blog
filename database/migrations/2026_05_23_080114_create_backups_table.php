<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->comment('备份文件名');
            $table->string('disk')->default('backups')->comment('存储磁盘');
            $table->unsignedBigInteger('size')->comment('文件大小（字节）');
            $table->string('type', 20)->default('full')->comment('类型：full全量、database数据库、files文件、incremental增量');
            $table->string('note')->nullable()->comment('备份备注');
            $table->string('status', 20)->default('pending')->comment('状态：pending等待、running运行中、completed完成、failed失败');
            $table->boolean('is_scheduled')->default(false)->comment('定时备份标记');
            $table->timestamp('schedule_time')->nullable()->comment('计划执行时间');
            $table->timestamp('started_at')->nullable()->comment('开始时间');
            $table->timestamp('completed_at')->nullable()->comment('完成时间');
            $table->text('error_message')->nullable()->comment('错误信息');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
