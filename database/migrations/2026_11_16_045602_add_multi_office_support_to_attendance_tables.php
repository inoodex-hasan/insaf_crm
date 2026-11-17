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
        if (Schema::hasTable('attendance_settings') && !Schema::hasColumn('attendance_settings', 'office_locations')) {
            Schema::table('attendance_settings', function (Blueprint $table) {
                $table->json('office_locations')->nullable()->after('geofence_radius_meters');
            });
        }

        if (Schema::hasTable('attendances') && !Schema::hasColumn('attendances', 'office_name')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('office_name')->nullable()->after('check_in_distance_meters');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendance_settings') && Schema::hasColumn('attendance_settings', 'office_locations')) {
            Schema::table('attendance_settings', function (Blueprint $table) {
                $table->dropColumn('office_locations');
            });
        }

        if (Schema::hasTable('attendances') && Schema::hasColumn('attendances', 'office_name')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('office_name');
            });
        }
    }
};
