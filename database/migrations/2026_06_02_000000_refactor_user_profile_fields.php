<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. author_profiles 新增 display_name 和 company
        Schema::table('author_profiles', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('slug')->comment('展示名称/笔名');
            $table->string('company')->nullable()->after('role_title')->comment('公司/组织名称');
        });

        // 2. users 删除 avatar、bio、github、twitter、linkedin
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'bio', 'github', 'twitter', 'linkedin']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password')->comment('头像URL');
            $table->text('bio')->nullable()->after('avatar')->comment('个人简介');
            $table->string('github')->nullable()->after('bio')->comment('GitHub');
            $table->string('twitter')->nullable()->after('github')->comment('Twitter');
            $table->string('linkedin')->nullable()->after('twitter')->comment('LinkedIn');
        });

        Schema::table('author_profiles', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'company']);
        });
    }
};
