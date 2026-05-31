<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 将 password 列从 VARCHAR(255) 改为 TEXT，
     * 以容纳 Laravel encrypted cast 生成的加密字符串。
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE mail_configs MODIFY password TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE mail_configs MODIFY password VARCHAR(255) NULL');
    }
};
