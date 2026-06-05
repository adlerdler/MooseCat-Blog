<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete()->comment('用户ID，关联users表（访客可为空）');
            $table->morphs('interactable');
            $table->string('type', 20)->default('like')->comment('类型：like点赞、bookmark收藏');
            $table->char('visitor_id', 32)->nullable()->comment('访客唯一标识MD5哈希');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->string('user_agent', 512)->nullable()->comment('浏览器User-Agent');
            $table->timestamps();

            $table->unique(['user_id', 'interactable_id', 'interactable_type', 'type'], 'user_interaction_unique');
            $table->unique('visitor_id', 'visitor_interaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
