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
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->comment('分类ID');
            $table->string('title')->comment('标题');
            $table->text('description')->comment('描述');
            $table->string('format', 50)->comment('格式');
            $table->string('file_size', 50)->comment('文件大小');
            $table->string('image')->nullable()->comment('缩略图');
            $table->string('direct_link')->nullable()->comment('下载链接');
            $table->json('drives')->nullable()->comment('备用链接');
            $table->unsignedBigInteger('downloads_count')->default(0)->comment('下载数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
