<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('标识符');
            $table->string('label')->comment('名称');
            $table->string('color', 50)->comment('颜色');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(false)->comment('可用');
            $table->boolean('is_default')->default(false)->comment('默认');
            $table->string('preview_image')->nullable()->comment('预览图');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
