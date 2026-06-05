<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete()->comment('广告ID');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('用户ID');
            $table->string('type', 50)->comment('交互类型');
            $table->string('ip_address', 45)->comment('访问者IP地址');
            $table->string('user_agent')->nullable()->comment('访问者User-Agent');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_interactions');
    }
};
