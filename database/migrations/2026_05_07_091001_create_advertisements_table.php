<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('广告标题');
            $table->string('image_url')->comment('图片URL');
            $table->string('link_url')->comment('链接URL');
            $table->foreignId('position_id')->nullable()->constrained('ad_positions')->nullOnDelete()->comment('广告位ID');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->unsignedBigInteger('clicks_count')->default(0)->comment('点击数');
            $table->unsignedBigInteger('views_count')->default(0)->comment('展示数');
            $table->timestamp('start_date')->nullable()->comment('开始时间');
            $table->timestamp('end_date')->nullable()->comment('结束时间');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
