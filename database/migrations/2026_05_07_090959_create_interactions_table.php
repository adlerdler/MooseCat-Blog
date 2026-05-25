<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('用户ID');
            $table->morphs('interactable');
            $table->enum('type', ['like', 'bookmark'])->comment('类型');
            $table->timestamps();

            $table->unique(['user_id', 'interactable_id', 'interactable_type', 'type'], 'user_interaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
