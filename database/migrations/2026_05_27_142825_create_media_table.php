<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable()->unique();
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->nullableTimestamps();
        });

        Schema::create('media_manipulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('name');
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('media_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_copies');
        Schema::dropIfExists('media_manipulations');
        Schema::dropIfExists('media');
    }
};
