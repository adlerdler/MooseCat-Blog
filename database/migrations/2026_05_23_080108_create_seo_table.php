<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->string('meta_title')->nullable()->comment('SEO标题');
            $table->text('meta_description')->nullable()->comment('SEO描述');
            $table->string('meta_keywords')->nullable()->comment('SEO关键词');
            $table->string('google_analytics')->nullable()->comment('Google统计');
            $table->string('baidu_analytics')->nullable()->comment('百度统计');
            $table->boolean('sitemap')->default(true)->comment('站点地图');
            $table->boolean('robots')->default(true)->comment('robots.txt');
            $table->boolean('llm_txt')->default(false)->comment('llm.txt');
            $table->string('canonical_url')->nullable()->comment('规范URL');
            $table->string('og_image')->nullable()->comment('OG图片');
            $table->string('og_type')->default('website')->comment('OG类型');
            $table->string('twitter_card')->default('summary_large_image')->comment('Twitter卡片');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo');
    }
};
