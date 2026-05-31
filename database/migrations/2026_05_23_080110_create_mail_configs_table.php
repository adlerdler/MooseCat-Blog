<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_configs', function (Blueprint $table) {
            $table->id();
            $table->string('mailer', 50)->comment('驱动');
            $table->string('host')->nullable()->comment('主机');
            $table->integer('port')->nullable()->comment('端口');
            $table->string('username')->nullable()->comment('用户名');
            $table->text('password')->nullable()->comment('密码');
            $table->string('encryption', 20)->nullable()->comment('加密');
            $table->string('from_address')->comment('发件地址');
            $table->string('from_name')->comment('发件名称');
            $table->boolean('is_active')->default(false)->comment('启用');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_configs');
    }
};
