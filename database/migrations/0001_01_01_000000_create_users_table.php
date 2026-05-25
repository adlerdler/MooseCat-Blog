<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('用户名');
            $table->string('email')->unique()->comment('邮箱');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('password')->comment('密码');
            $table->string('avatar')->nullable()->comment('头像URL');
            $table->text('bio')->nullable()->comment('个人简介');
            $table->string('github')->nullable()->comment('GitHub');
            $table->string('twitter')->nullable()->comment('Twitter');
            $table->string('linkedin')->nullable()->comment('LinkedIn');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->comment('状态');
            $table->unsignedBigInteger('points')->default(0)->comment('积分');
            $table->boolean('notifications')->default(true)->comment('邮件通知');
            $table->boolean('comment_approval_alert')->default(true)->comment('评论提醒');
            $table->boolean('new_user_alert')->default(true)->comment('新用户提醒');
            $table->boolean('weekly_report')->default(false)->comment('周报');
            $table->boolean('digest_email')->default(false)->comment('摘要邮件');
            $table->enum('digest_frequency', ['daily', 'weekly', 'monthly'])->default('weekly')->comment('邮件频率');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
