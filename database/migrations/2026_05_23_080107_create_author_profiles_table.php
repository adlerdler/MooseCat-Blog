<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('author_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('role_label')->nullable();
            $table->string('role_title')->nullable();
            $table->string('status_label')->nullable();
            $table->string('status_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('social_links')->nullable();
            $table->json('expertise')->nullable();
            $table->json('skills')->nullable();
            $table->json('manifestos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_profiles');
    }
};
