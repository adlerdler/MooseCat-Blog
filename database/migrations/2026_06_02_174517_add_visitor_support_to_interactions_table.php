<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 上次失败残留的 varchar(64) 列，先删后重建为 char(32)
        if (Schema::hasColumn('interactions', 'visitor_id')) {
            DB::statement('ALTER TABLE interactions DROP COLUMN visitor_id');
        }

        Schema::table('interactions', function (Blueprint $table) {
            if (!Schema::hasColumn('interactions', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (!Schema::hasColumn('interactions', 'user_agent')) {
                $table->string('user_agent', 512)->nullable();
            }

            // char(32) = MD5(ip+ua+interactable_type+interactable_id+type)
            // 32 字节，加上单列索引远低于 1000 字节限制
            $table->char('visitor_id', 32)->nullable();

            $table->unique('visitor_id', 'visitor_interaction_unique');
        });
    }

    public function down(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropUnique('visitor_interaction_unique');
            $table->dropColumn(['visitor_id', 'ip_address', 'user_agent']);
        });
    }
};
