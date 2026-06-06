<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete()->comment('作者ID，关联users表');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->comment('分类ID，关联categories表');
            $table->string('title')->comment('资源标题');
            $table->text('description')->nullable()->comment('资源描述');
            $table->string('format', 50)->comment('文件格式');
            $table->string('file_size', 50)->comment('文件大小');
            $table->string('image')->nullable()->comment('缩略图URL');
            $table->string('direct_link')->nullable()->comment('下载链接');
            $table->json('drives')->nullable()->comment('备用下载链接JSON');
            $table->unsignedBigInteger('downloads_count')->default(0)->comment('下载次数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞次数');
            $table->timestamps();

            // 业务索引
            $table->index('created_at'); // 资源列表按时间排序
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
