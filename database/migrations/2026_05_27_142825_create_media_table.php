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
            $table->uuid()->nullable()->unique()->comment('UUID唯一标识');
            $table->morphs('model');
            $table->string('collection_name')->comment('集合名称');
            $table->string('name')->comment('文件名称');
            $table->string('file_name')->comment('文件名');
            $table->string('mime_type')->nullable()->comment('MIME类型');
            $table->string('disk')->comment('存储磁盘');
            $table->string('conversions_disk')->nullable()->comment('转换文件存储磁盘');
            $table->unsignedBigInteger('size')->comment('文件大小（字节）');
            $table->json('manipulations')->comment('图片操作配置');
            $table->json('custom_properties')->comment('自定义属性');
            $table->json('generated_conversions')->comment('已生成的转换');
            $table->json('responsive_images')->comment('响应式图片配置');
            $table->unsignedInteger('order_column')->nullable()->comment('排序序号');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('media_manipulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete()->comment('媒体ID');
            $table->string('name')->comment('操作名称');
            $table->json('value')->comment('操作配置JSON');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('media_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete()->comment('媒体ID');
            $table->string('name')->comment('副本名称');
            $table->string('file_name')->comment('文件名');
            $table->string('mime_type')->nullable()->comment('MIME类型');
            $table->string('disk')->comment('存储磁盘');
            $table->unsignedBigInteger('size')->comment('文件大小（字节）');
            $table->unsignedInteger('order_column')->nullable()->comment('排序序号');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_copies');
        Schema::dropIfExists('media_manipulations');
        Schema::dropIfExists('media');
    }
};
