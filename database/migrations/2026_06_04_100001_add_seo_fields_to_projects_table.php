<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('slug')->comment('SEO标题');
            $table->string('meta_description', 500)->nullable()->after('meta_title')->comment('SEO描述');
            $table->string('meta_keywords', 500)->nullable()->after('meta_description')->comment('SEO关键词');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords']);
        });
    }
};
