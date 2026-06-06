<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete()->comment('父分类ID');
            $table->string('name')->unique()->comment('分类名称');
            $table->string('slug')->unique()->comment('URL标识符');
            $table->string('description')->nullable()->comment('分类描述');
            $table->string('status', 50)->default('active')->comment('状态');
            $table->integer('sort_order')->default(0)->comment('排序序号');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // 业务索引
            $table->index(['status', 'sort_order']); // 分类列表
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
