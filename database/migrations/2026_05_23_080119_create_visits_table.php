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
            $table->unsignedBigInteger('visitable_id')->nullable()->comment('多态关联ID（详情页有具体模型，列表页/首页为null）');
            $table->string('visitable_type')->nullable()->comment('多态关联类型');
            $table->string('page', 500)->nullable()->comment('通用页面路径（列表页/首页等无模型关联的页面使用）');
            $table->string('title', 255)->nullable()->comment('页面标题');
            $table->string('ip_address', 45)->comment('访问者IP地址');
            $table->string('user_agent')->nullable()->comment('访问者User-Agent');
            $table->string('referrer', 500)->nullable()->comment('来源页面URL');
            $table->timestamp('created_at');
            $table->index(['visitable_id', 'visitable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
