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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('interactable'); // interactable_id and interactable_type
            $table->enum('type', ['like', 'bookmark']);
            $table->timestamps();

            $table->unique(['user_id', 'interactable_id', 'interactable_type', 'type'], 'user_interaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
