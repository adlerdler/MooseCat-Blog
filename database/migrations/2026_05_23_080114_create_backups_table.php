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
            $table->string('filename')->comment('文件名');
            $table->unsignedBigInteger('size')->comment('大小');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->comment('状态');
            $table->enum('type', ['full', 'database', 'files'])->comment('类型');
            $table->timestamp('started_at')->comment('开始时间');
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
