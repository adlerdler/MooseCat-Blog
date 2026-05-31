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
            $table->string('title')->comment('标题');
            $table->string('slug')->nullable()->after('title')->comment('URL标识符');
            $table->text('description')->comment('描述');
            $table->string('video_id')->nullable()->comment('视频ID');
            $table->string('video_url')->nullable()->after('description')->comment('视频链接');
            $table->string('cover_image')->nullable()->after('video_url')->comment('封面图');
            $table->string('status')->default('draft')->after('cover_image')->comment('状态');
            $table->unsignedBigInteger('category_id')->nullable()->after('status')->comment('分类ID');
            $table->enum('platform', ['youtube', 'bilibili'])->comment('平台');
            $table->string('thumbnail')->nullable()->comment('缩略图');
            $table->string('duration', 50)->nullable()->comment('时长');
            $table->unsignedBigInteger('views_count')->default(0)->comment('播放数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
