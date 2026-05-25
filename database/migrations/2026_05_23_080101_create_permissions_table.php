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
            $table->string('name')->unique()->comment('权限名');
            $table->string('guard_name')->default('web')->comment('守卫');
            $table->string('label')->nullable()->comment('标签');
            $table->text('description')->nullable()->comment('描述');
            $table->string('program_id', 100)->nullable()->comment('模块标识');
            $table->timestamps();
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
