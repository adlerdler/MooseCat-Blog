<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('author_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete()->comment('用户ID');
            $table->string('slug')->unique()->comment('URL标识符');
            $table->text('bio')->nullable()->comment('简介');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('role_label')->nullable()->comment('角色标签');
            $table->string('role_title')->nullable()->comment('头衔');
            $table->string('status_label')->nullable()->comment('状态标签');
            $table->string('status_text')->nullable()->comment('状态文本');
            $table->boolean('is_active')->default(true)->comment('可见');
            $table->json('social_links')->nullable()->comment('社交链接');
            $table->json('expertise')->nullable()->comment('专业领域');
            $table->json('skills')->nullable()->comment('技能');
            $table->json('manifestos')->nullable()->comment('理念');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_profiles');
    }
};
