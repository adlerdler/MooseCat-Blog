<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('标识符');
            $table->string('subject')->comment('主题');
            $table->longText('content')->comment('内容');
            $table->string('description')->nullable()->comment('模板描述');
            $table->json('variables')->nullable()->comment('变量');
            $table->boolean('is_active')->default(true)->comment('启用');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
