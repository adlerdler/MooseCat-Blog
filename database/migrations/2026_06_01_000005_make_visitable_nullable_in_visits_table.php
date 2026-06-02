<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // 页面级访问（首页、列表页）无具体模型关联，visitable_id 和 visitable_type 可为 null
            $table->unsignedBigInteger('visitable_id')->nullable()->change();
            $table->string('visitable_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->unsignedBigInteger('visitable_id')->nullable(false)->change();
            $table->string('visitable_type')->nullable(false)->change();
        });
    }
};
