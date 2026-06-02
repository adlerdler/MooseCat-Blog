<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // page: 页面路径（如 /blog, /projects/1），用于非多态模型的页面访问记录
            $table->string('page', 500)->nullable()->after('visitable_type');
            // title: 页面标题，用于仪表盘展示
            $table->string('title', 255)->nullable()->after('page');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['page', 'title']);
        });
    }
};
