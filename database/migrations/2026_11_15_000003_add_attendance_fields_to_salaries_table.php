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
        Schema::table('salaries', function (Blueprint $table) {
            $table->decimal('attendance_deduction', 10, 2)->default(0.00)->after('other_deductions');
            $table->unsignedSmallInteger('late_days')->default(0)->after('attendance_deduction');
            $table->unsignedSmallInteger('absent_days')->default(0)->after('late_days');
            $table->unsignedSmallInteger('half_days')->default(0)->after('absent_days');
            $table->text('attendance_details')->nullable()->after('half_days'); // JSON or textual breakdown
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn([
                'attendance_deduction',
                'late_days',
                'absent_days',
                'half_days',
                'attendance_details',
            ]);
        });
    }
};
