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
            $table->string('name', 100)->unique()->comment('主题标识符');
            $table->string('label')->comment('主题名称');
            $table->string('color', 50)->comment('主题颜色');
            $table->integer('sort_order')->default(0)->comment('排序序号');
            $table->boolean('is_active')->default(false)->comment('是否可用');
            $table->boolean('is_default')->default(false)->comment('是否默认主题');
            $table->string('preview_image')->nullable()->comment('预览图URL');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
