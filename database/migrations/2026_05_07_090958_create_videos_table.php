<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('视频标题');
            $table->string('slug')->nullable()->comment('URL标识符');
            $table->text('description')->nullable()->comment('视频描述');
            $table->string('video_id')->nullable()->comment('平台视频ID');
            $table->string('video_url')->nullable()->comment('视频链接');
            $table->string('cover_image')->nullable()->comment('封面图片URL');
            $table->string('status')->default('draft')->comment('状态：draft草稿、published已发布');
            $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete()->comment('作者ID，关联users表');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->comment('分类ID，关联categories表');
            $table->string('platform')->default('youtube')->comment('平台：youtube、bilibili、local');
            $table->string('thumbnail')->nullable()->comment('缩略图URL');
            $table->string('duration', 50)->nullable()->comment('视频时长');
            $table->unsignedBigInteger('views_count')->default(0)->comment('播放次数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞次数');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->string('meta_title')->nullable()->comment('SEO标题');
            $table->string('meta_description', 500)->nullable()->comment('SEO描述');
            $table->string('meta_keywords', 500)->nullable()->comment('SEO关键词');
            $table->timestamps();

            // 业务索引
            $table->index(['status', 'published_at']); // 视频列表页
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
