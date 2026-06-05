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
            $table->string('mailer', 50)->comment('驱动：smtp、sendmail、log');
            $table->string('host')->nullable()->comment('SMTP主机地址');
            $table->integer('port')->nullable()->comment('SMTP端口');
            $table->string('username')->nullable()->comment('SMTP用户名');
            $table->text('password')->nullable()->comment('SMTP密码（加密存储）');
            $table->string('encryption', 20)->nullable()->comment('加密方式：tls、ssl、null');
            $table->string('from_address')->comment('发件地址');
            $table->string('from_name')->comment('发件名称');
            $table->boolean('is_active')->default(false)->comment('是否启用');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_configs');
    }
};
