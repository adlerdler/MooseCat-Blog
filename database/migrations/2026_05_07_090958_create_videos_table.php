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
            $table->text('description')->comment('描述');
            $table->string('video_id')->comment('视频ID');
            $table->enum('platform', ['youtube', 'bilibili'])->comment('平台');
            $table->string('thumbnail')->nullable()->comment('缩略图');
            $table->string('duration', 50)->nullable()->comment('时长');
            $table->unsignedBigInteger('views_count')->default(0)->comment('播放数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
