<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 修复 roles 和 permissions 表结构，对齐 Spatie Permission 官方规范
     */
    public function up(): void
    {
        // 修复 roles 表字段长度
        DB::statement('ALTER TABLE `roles` MODIFY `name` VARCHAR(125) NOT NULL');
        DB::statement('ALTER TABLE `roles` MODIFY `guard_name` VARCHAR(125) NOT NULL');

        // 添加或修复自定义字段
        if (!Schema::hasColumn('roles', 'value')) {
            DB::statement("ALTER TABLE `roles` ADD `value` VARCHAR(255) NULL COMMENT '角色值' AFTER `name`");
        } else {
            DB::statement("ALTER TABLE `roles` MODIFY `value` VARCHAR(255) NULL COMMENT '角色值'");
        }
        if (!Schema::hasColumn('roles', 'label')) {
            DB::statement("ALTER TABLE `roles` ADD `label` VARCHAR(255) NULL COMMENT '标签' AFTER `guard_name`");
        }
        if (!Schema::hasColumn('roles', 'color')) {
            DB::statement("ALTER TABLE `roles` ADD `color` VARCHAR(50) NULL COMMENT '颜色' AFTER `label`");
        }
        if (!Schema::hasColumn('roles', 'description')) {
            DB::statement("ALTER TABLE `roles` ADD `description` TEXT NULL COMMENT '描述' AFTER `color`");
        }

        // 删除旧的唯一索引并添加联合唯一索引
        $indexes = DB::select("SHOW INDEX FROM `roles` WHERE Non_unique = 0");
        foreach ($indexes as $index) {
            if ($index->Column_name === 'name' || $index->Column_name === 'value') {
                DB::statement("ALTER TABLE `roles` DROP INDEX `{$index->Key_name}`");
            }
        }
        DB::statement('ALTER TABLE `roles` ADD UNIQUE INDEX `roles_name_guard_name_unique` (`name`, `guard_name`)');

        // 修复 permissions 表字段长度
        DB::statement('ALTER TABLE `permissions` MODIFY `name` VARCHAR(125) NOT NULL');
        DB::statement('ALTER TABLE `permissions` MODIFY `guard_name` VARCHAR(125) NOT NULL DEFAULT \'web\'');

        // 删除旧的唯一索引并添加联合唯一索引
        $indexes = DB::select("SHOW INDEX FROM `permissions` WHERE Non_unique = 0");
        foreach ($indexes as $index) {
            if ($index->Column_name === 'name') {
                DB::statement("ALTER TABLE `permissions` DROP INDEX `{$index->Key_name}`");
            }
        }
        DB::statement('ALTER TABLE `permissions` ADD UNIQUE INDEX `permissions_name_guard_name_unique` (`name`, `guard_name`)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `roles` DROP INDEX `roles_name_guard_name_unique`');
        DB::statement('ALTER TABLE `roles` MODIFY `name` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `roles` MODIFY `guard_name` VARCHAR(255) NOT NULL');
        if (Schema::hasColumn('roles', 'value')) {
            DB::statement('ALTER TABLE `roles` DROP COLUMN `value`');
            DB::statement('ALTER TABLE `roles` DROP COLUMN `label`');
            DB::statement('ALTER TABLE `roles` DROP COLUMN `color`');
            DB::statement('ALTER TABLE `roles` DROP COLUMN `description`');
        }

        DB::statement('ALTER TABLE `permissions` DROP INDEX `permissions_name_guard_name_unique`');
        DB::statement('ALTER TABLE `permissions` MODIFY `name` VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE `permissions` MODIFY `guard_name` VARCHAR(255) NOT NULL DEFAULT \'web\'');
    }
};
