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
            $table->foreignId('level_id')->nullable()->constrained('user_levels')->nullOnDelete()->comment('用户等级ID，关联user_levels表');
            $table->string('name')->comment('用户名');
            $table->string('email')->unique()->comment('邮箱');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('password')->comment('密码');
            $table->string('status')->default('active')->comment('状态：active活跃、inactive禁用、suspended封禁');
            $table->unsignedBigInteger('points')->default(0)->comment('积分');
            $table->boolean('notifications')->default(true)->comment('邮件通知开关');
            $table->boolean('comment_approval_alert')->default(true)->comment('评论审核提醒');
            $table->boolean('new_user_alert')->default(true)->comment('新用户注册提醒');
            $table->boolean('weekly_report')->default(false)->comment('周报订阅');
            $table->boolean('digest_email')->default(false)->comment('摘要邮件订阅');
            $table->string('digest_frequency')->default('weekly')->comment('摘要频率：daily每天、weekly每周、monthly每月');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
            $table->rememberToken()->comment('记住登录令牌');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()->comment('邮箱');
            $table->string('token')->comment('重置令牌');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->comment('用户ID');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->text('user_agent')->nullable()->comment('浏览器User-Agent');
            $table->longText('payload')->comment('会话数据');
            $table->integer('last_activity')->index()->comment('最后活动时间戳');
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
