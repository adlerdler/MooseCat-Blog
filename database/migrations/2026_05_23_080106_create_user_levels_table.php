<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('名称');
            $table->integer('level')->comment('等级');
            $table->integer('min_points')->comment('最低积分');
            $table->integer('max_points')->nullable()->comment('最高积分');
            $table->integer('discount')->default(0)->comment('折扣');
            $table->string('color', 50)->comment('颜色');
            $table->string('icon', 100)->nullable()->comment('图标');
            $table->string('description', 500)->nullable()->comment('描述');
            $table->json('benefits')->nullable()->comment('权益');
            $table->boolean('is_active')->default(true)->comment('可用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_levels');
    }
};
