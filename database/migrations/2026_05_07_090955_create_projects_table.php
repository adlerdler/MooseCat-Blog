<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->text('description')->comment('简短描述');
            $table->longText('long_description')->nullable()->comment('详细描述');
            $table->string('client')->nullable()->comment('客户');
            $table->string('role')->nullable()->comment('角色');
            $table->integer('year')->comment('年份');
            $table->string('image')->nullable()->comment('封面图');
            $table->string('url')->nullable()->comment('在线地址');
            $table->string('github_url')->nullable()->comment('GitHub地址');
            $table->json('technologies')->nullable()->comment('技术栈');
            $table->enum('status', ['planning', 'in-progress', 'completed'])->default('completed')->comment('状态');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->unsignedBigInteger('views_count')->default(0)->comment('浏览数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞数');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
