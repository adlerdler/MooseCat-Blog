<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('代码');
            $table->string('name')->comment('名称');
            $table->string('native_name')->comment('母语名');
            $table->string('flag', 20)->nullable()->comment('国旗');
            $table->string('file_path', 200)->nullable()->comment('文件路径');
            $table->string('direction', 3)->default('ltr')->comment('方向');
            $table->boolean('is_active')->default(true)->comment('可用');
            $table->boolean('is_default')->default(false)->comment('默认');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
