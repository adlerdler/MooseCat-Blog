<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ad_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('标识符');
            $table->string('label_key')->comment('翻译键');
            $table->text('description')->nullable()->comment('描述');
            $table->integer('default_width')->comment('默认宽度');
            $table->integer('default_height')->comment('默认高度');
            $table->boolean('is_active')->default(true)->comment('启用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_positions');
    }
};
