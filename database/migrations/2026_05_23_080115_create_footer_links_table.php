<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->comment('类型');
            $table->string('platform', 100)->nullable()->comment('平台名称');
            $table->string('icon_name', 100)->nullable()->comment('图标名称');
            $table->string('label')->comment('显示文本');
            $table->string('url', 500)->nullable()->comment('链接地址');
            $table->string('icon', 100)->nullable()->comment('图标CSS类名');
            $table->integer('sort_order')->default(0)->comment('排序序号');
            $table->boolean('is_active')->default(true)->comment('是否可见');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};
