<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete()->comment('文章ID，关联posts表');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete()->comment('父评论ID，自关联');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('用户ID，关联users表');
            $table->string('name')->nullable()->comment('评论人名称');
            $table->string('email')->nullable()->comment('评论人邮箱');
            $table->text('body')->comment('评论内容');
            $table->boolean('is_approved')->default(true)->comment('审核状态');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->string('user_agent')->nullable()->comment('浏览器User-Agent');
            $table->boolean('is_admin')->default(false)->comment('管理员回复标记');
            $table->timestamps();

            $table->index(['post_id', 'is_approved', 'created_at'], 'idx_comments_post_approved_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
