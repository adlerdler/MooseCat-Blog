<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->string('slug')->unique()->comment('URL标识符');
            $table->text('excerpt')->comment('摘要');
            $table->longText('content')->comment('内容');
            $table->string('cover_image')->nullable()->comment('封面图');
            $table->string('color', 50)->default('black')->comment('主题色');
            $table->string('status', 50)->default('draft')->comment('文章状态');
            $table->unsignedBigInteger('views_count')->default(0)->comment('浏览数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->string('meta_title')->nullable()->comment('SEO标题');
            $table->string('meta_description', 500)->nullable()->comment('SEO描述');
            $table->string('meta_keywords')->nullable()->comment('SEO关键词');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete()->comment('作者ID');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->comment('分类ID');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // 业务索引
            $table->index(['status', 'published_at']); // 首页、列表页频繁使用
            $table->index(['category_id', 'status']);  // 分类列表页
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
