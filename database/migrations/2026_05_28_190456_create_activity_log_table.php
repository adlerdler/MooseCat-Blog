<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable()->comment('日志名称');
            $table->text('description')->comment('日志描述');
            $table->string('event')->nullable()->comment('事件名称');
            $table->nullableMorphs('subject', 'subject')->comment('操作主体（多态关联）');
            $table->nullableMorphs('causer', 'causer')->comment('操作者（多态关联）');
            $table->json('properties')->nullable()->comment('额外属性JSON');
            $table->uuid('batch_uuid')->nullable()->comment('批次UUID');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->string('user_agent')->nullable()->comment('User-Agent');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('log_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
