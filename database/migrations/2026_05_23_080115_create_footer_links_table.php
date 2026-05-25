<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['social_link', 'nav_link', 'brand_info'])->comment('类型');
            $table->string('platform', 100)->nullable()->comment('平台');
            $table->string('icon_name', 100)->nullable()->comment('图标');
            $table->string('label')->comment('文本');
            $table->string('url', 500)->nullable()->comment('链接');
            $table->string('icon', 100)->nullable()->comment('图标类');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('可见');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};
