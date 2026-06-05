<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_points_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('用户ID');
            $table->integer('points')->comment('积分变动数量');
            $table->string('type', 100)->comment('积分变动类型');
            $table->text('description')->nullable()->comment('变动描述');
            $table->foreignId('reference_id')->nullable()->comment('关联记录ID');
            $table->string('reference_type', 255)->nullable()->comment('关联记录类型');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_points_history');
    }
};
