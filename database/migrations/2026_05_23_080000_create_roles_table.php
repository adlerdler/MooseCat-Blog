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
        // 创建 roles 表（Spatie Permission 兼容 + 自定义字段）
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 125);
            $table->string('guard_name', 125);
            $table->string('value')->nullable()->comment('角色值');
            $table->string('label')->nullable()->comment('标签');
            $table->string('color', 50)->nullable()->comment('颜色');
            $table->text('description')->nullable()->comment('描述');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
