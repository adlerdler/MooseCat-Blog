<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->comment('分组');
            $table->string('key', 150)->comment('键名');
            $table->json('text')->comment('翻译文本');
            $table->string('description', 500)->nullable()->comment('说明');
            $table->boolean('is_active')->default(true)->comment('启用');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
