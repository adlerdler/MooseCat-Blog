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
            $table->string('title')->nullable()->comment('标题');
            $table->text('content')->comment('内容');
            $table->string('mood')->nullable()->comment('心情');
            $table->string('weather')->nullable()->comment('天气');
            $table->date('date')->nullable()->comment('日期');
            $table->boolean('is_public')->default(true)->comment('公开');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
