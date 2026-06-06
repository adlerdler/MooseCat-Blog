<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('用户ID');
            $table->string('title')->nullable()->comment('日记标题');
            $table->text('content')->comment('日记内容');
            $table->string('mood')->nullable()->comment('心情');
            $table->string('weather')->nullable()->comment('天气');
            $table->date('date')->nullable()->comment('日记日期');
            $table->boolean('is_public')->default(true)->comment('是否公开');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // 业务索引
            $table->index(['is_public', 'date']); // 公开日记列表
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
