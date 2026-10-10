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
        if (!Schema::hasColumn('applications', 'vfs_note')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->text('vfs_note')->nullable()->after('vfs_result');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('applications', 'vfs_note')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('vfs_note');
            });
        }
    }
};
