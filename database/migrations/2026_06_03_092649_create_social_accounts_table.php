<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('用户ID');
            $table->string('provider', 20)->comment('第三方平台标识');
            $table->string('provider_id')->comment('第三方平台用户ID');
            $table->json('provider_data')->nullable()->comment('OAuth返回的原始数据');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['provider', 'provider_id']);
            $table->index(['provider', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
