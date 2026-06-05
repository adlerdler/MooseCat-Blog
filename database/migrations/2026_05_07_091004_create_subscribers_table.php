<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->comment('订阅邮箱');
            $table->string('name')->nullable()->comment('订阅者名称');
            $table->string('source', 100)->nullable()->comment('订阅来源');
            $table->boolean('is_active')->default(true)->comment('是否活跃');
            $table->timestamp('subscribed_at')->nullable()->comment('订阅时间');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
