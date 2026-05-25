<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('用户ID');
            $table->string('filename')->comment('文件名');
            $table->string('original_name')->comment('原始名');
            $table->string('mime_type', 100)->comment('MIME类型');
            $table->unsignedBigInteger('file_size')->comment('大小');
            $table->string('url')->comment('URL');
            $table->string('thumbnail_url')->nullable()->comment('缩略图');
            $table->string('alt_text')->nullable()->comment('替代文本');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
