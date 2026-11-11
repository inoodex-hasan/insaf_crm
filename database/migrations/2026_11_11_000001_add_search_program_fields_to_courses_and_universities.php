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
        // 1. Add fields to courses table for Search Program module
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'study_area')) {
                $table->string('study_area')->nullable()->after('degree_level')->index();
            }
            if (!Schema::hasColumn('courses', 'discipline_area')) {
                $table->string('discipline_area')->nullable()->after('study_area')->index();
            }
            if (!Schema::hasColumn('courses', 'program_level')) {
                $table->string('program_level', 50)->nullable()->after('discipline_area')->index();
            }
            if (!Schema::hasColumn('courses', 'duration_type')) {
                $table->string('duration_type', 50)->nullable()->after('duration')->index();
            }
            if (!Schema::hasColumn('courses', 'requirements')) {
                $table->json('requirements')->nullable()->after('duration_type');
            }
            if (!Schema::hasColumn('courses', 'available_intake_months')) {
                $table->json('available_intake_months')->nullable()->after('requirements');
            }
            if (!Schema::hasColumn('courses', 'intake_years')) {
                $table->json('intake_years')->nullable()->after('available_intake_months');
            }
            if (!Schema::hasColumn('courses', 'esl_available')) {
                $table->boolean('esl_available')->default(false)->after('intake_years');
            }
            if (!Schema::hasColumn('courses', 'stem_program')) {
                $table->boolean('stem_program')->default(false)->after('esl_available');
            }
            if (!Schema::hasColumn('courses', 'currency')) {
                $table->string('currency', 10)->default('USD')->after('tuition_fee');
            }
        });

        // 2. Add state_province to universities table
        Schema::table('universities', function (Blueprint $table) {
            if (!Schema::hasColumn('universities', 'state_province')) {
                $table->string('state_province')->nullable()->after('country_id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'study_area',
                'discipline_area',
                'program_level',
                'duration_type',
                'requirements',
                'available_intake_months',
                'intake_years',
                'esl_available',
                'stem_program',
                'currency',
            ]);
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn(['state_province']);
        });
    }
};

