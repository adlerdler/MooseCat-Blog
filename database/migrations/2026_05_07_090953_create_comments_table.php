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
            $table->foreignId('post_id')->constrained()->cascadeOnDelete()->comment('文章ID');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete()->comment('父评论ID');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('用户ID');
            $table->string('name')->nullable()->comment('名称');
            $table->string('email')->nullable()->comment('邮箱');
            $table->text('body')->comment('内容');
            $table->boolean('is_approved')->default(true)->comment('已审核');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->string('user_agent')->nullable()->comment('User-Agent');
            $table->timestamps();

            $table->index(['post_id', 'is_approved', 'created_at'], 'idx_comments_post_approved_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
