<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seo', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique()->comment('页面键');
            $table->string('title')->comment('标题');
            $table->string('description', 500)->comment('描述');
            $table->string('keywords', 500)->nullable()->comment('关键词');
            $table->string('og_image')->nullable()->comment('OG图片');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seo');
    }
};
