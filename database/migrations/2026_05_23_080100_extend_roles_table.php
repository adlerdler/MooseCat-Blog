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
        // Spatie Permission 已经创建了 roles 表
        // 这个迁移用于扩展 roles 表，添加业务字段
        Schema::table('roles', function (Blueprint $table) {
            $table->string('value')->unique()->after('name')->comment('角色值');
            $table->string('label')->nullable()->after('guard_name')->comment('标签');
            $table->string('color', 50)->nullable()->after('label')->comment('颜色');
            $table->text('description')->nullable()->after('color')->comment('描述');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['value', 'label', 'color', 'description']);
        });
    }
};
