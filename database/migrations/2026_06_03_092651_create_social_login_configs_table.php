<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_login_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 20)->unique();
            $table->string('name', 50);
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable()->comment('AES-256加密存储');
            $table->string('redirect_uri')->nullable();
            $table->boolean('enabled')->default(false);
            $table->json('extra_config')->nullable()->comment('扩展配置如Apple team_id, key_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_login_configs');
    }
};
