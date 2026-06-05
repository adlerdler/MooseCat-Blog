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
            $table->string('title')->comment('项目标题');
            $table->string('slug')->unique()->comment('URL标识符');
            $table->text('description')->nullable()->comment('简短描述');
            $table->longText('long_description')->nullable()->comment('详细描述');
            $table->string('client')->nullable()->comment('客户名称');
            $table->string('role')->nullable()->comment('担任角色');
            $table->integer('year')->comment('项目年份');
            $table->string('image')->nullable()->comment('封面图片URL');
            $table->string('url')->nullable()->comment('在线地址');
            $table->string('github_url')->nullable()->comment('GitHub仓库地址');
            $table->json('technologies')->nullable()->comment('技术栈JSON数组');
            $table->string('meta_title')->nullable()->comment('SEO标题');
            $table->string('meta_description', 500)->nullable()->comment('SEO描述');
            $table->string('meta_keywords', 500)->nullable()->comment('SEO关键词');
            $table->string('status')->default('completed')->comment('状态：planning规划中、in-progress进行中、completed已完成');
            $table->integer('sort_order')->default(0)->comment('排序序号');
            $table->unsignedBigInteger('views_count')->default(0)->comment('浏览次数');
            $table->unsignedBigInteger('likes_count')->default(0)->comment('点赞次数');
            $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete()->comment('作者ID，关联users表');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
