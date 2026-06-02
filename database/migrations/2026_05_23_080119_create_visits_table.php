<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            // 多态关联（详情页有具体模型，列表页/首页为 null）
            $table->unsignedBigInteger('visitable_id')->nullable();
            $table->string('visitable_type')->nullable();
            // 通用页面字段（列表页/首页等无模型关联的页面使用）
            $table->string('page', 500)->nullable();
            $table->string('title', 255)->nullable();
            // 访问元数据
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->timestamp('created_at');
            $table->index(['visitable_id', 'visitable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
