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
            $table->string('name', 125)->comment('角色名称');
            $table->string('guard_name', 125)->comment('守卫名称');
            $table->string('value')->nullable()->comment('角色值');
            $table->string('label')->nullable()->comment('角色标签');
            $table->string('color', 50)->nullable()->comment('角色颜色');
            $table->text('description')->nullable()->comment('角色描述');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
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
