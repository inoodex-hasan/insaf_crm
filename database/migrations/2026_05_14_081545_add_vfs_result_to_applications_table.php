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
        if (!Schema::hasColumn('applications', 'vfs_result')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('vfs_result')->default('pending')->after('vfs_appointment_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('applications', 'vfs_result')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('vfs_result');
            });
        }
    }
};
