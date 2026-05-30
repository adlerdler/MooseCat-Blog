<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Rename filename to file_name
            $table->renameColumn('filename', 'file_name');
            
            // Add uuid column
            $table->uuid('uuid')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->renameColumn('file_name', 'filename');
            $table->dropColumn('uuid');
        });
    }
};
