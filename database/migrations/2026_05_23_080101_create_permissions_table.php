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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 125)->comment('权限名称');
            $table->string('guard_name', 125)->default('web')->comment('守卫名称');
            $table->string('label')->nullable()->comment('权限标签');
            $table->text('description')->nullable()->comment('权限描述');
            $table->string('program_id', 100)->nullable()->comment('模块标识');
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
        Schema::dropIfExists('permissions');
    }
};
