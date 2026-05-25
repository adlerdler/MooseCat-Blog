<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // 基本信息
            $table->string('name')->default('ARCHYX')->comment('站点名');
            $table->text('description')->nullable()->comment('描述');
            $table->string('site_url')->nullable()->comment('站点URL');
            $table->string('copyright')->nullable()->comment('版权');
            $table->string('logo')->nullable()->comment('Logo');
            $table->string('favicon')->nullable()->comment('Favicon');
            $table->string('timezone')->default('Asia/Shanghai')->comment('时区');
            
            // 功能开关
            $table->boolean('maintenance')->default(false)->comment('维护模式');
            $table->boolean('author_bio')->default(false)->comment('作者简介');
            $table->boolean('comments')->default(true)->comment('评论');
            $table->boolean('registration')->default(true)->comment('注册');
            $table->boolean('comment_approval')->default(false)->comment('评论审核');
            $table->boolean('newsletter')->default(true)->comment('邮件订阅');
            $table->boolean('social_login')->default(false)->comment('社交登录');
            $table->boolean('search')->default(true)->comment('搜索');
            
            // 性能优化
            $table->boolean('cache')->default(true)->comment('缓存');
            $table->integer('cache_duration')->default(3600)->comment('缓存时长');
            $table->boolean('minification')->default(true)->comment('代码压缩');
            $table->boolean('lazy_load')->default(true)->comment('图片懒加载');
            $table->boolean('cdn')->default(false)->comment('CDN');
            $table->string('cdn_url')->nullable()->comment('CDN地址');
            
            // 文件上传
            $table->integer('max_upload_size')->default(10)->comment('最大上传MB');
            $table->json('file_types')->nullable()->comment('允许类型');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
