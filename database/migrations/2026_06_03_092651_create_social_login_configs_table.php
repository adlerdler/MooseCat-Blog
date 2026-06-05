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
            $table->string('provider', 20)->unique()->comment('第三方平台标识');
            $table->string('name', 50)->comment('平台名称');
            $table->string('client_id')->nullable()->comment('客户端ID');
            $table->text('client_secret')->nullable()->comment('客户端密钥（AES-256加密存储）');
            $table->string('redirect_uri')->nullable()->comment('回调地址');
            $table->boolean('enabled')->default(false)->comment('是否启用');
            $table->json('extra_config')->nullable()->comment('扩展配置（如Apple team_id, key_id）');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_login_configs');
    }
};
