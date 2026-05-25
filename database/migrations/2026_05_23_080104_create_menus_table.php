<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['front', 'admin'])->comment('类型');
            $table->foreignId('parent_id')->nullable()->comment('父菜单ID');
            $table->string('label_key')->comment('翻译键');
            $table->string('icon_name', 100)->nullable()->comment('图标');
            $table->string('path')->nullable()->comment('路径');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('可见');
            $table->string('component_name')->nullable()->comment('组件名');
            $table->timestamps();
        });

        // 添加自引用外键约束
        Schema::table('menus', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('menus')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
