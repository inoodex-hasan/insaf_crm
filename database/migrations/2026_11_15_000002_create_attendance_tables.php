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
        // 1. Attendance Settings Table (Super Admin Configurable)
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->default('Insaf Educancy Main Office');
            $table->decimal('office_latitude', 10, 8)->default(23.79250000); // Dhaka center / Banani default
            $table->decimal('office_longitude', 11, 8)->default(90.40780000);
            $table->integer('geofence_radius_meters')->default(150); // 150m boundary
            $table->boolean('enforce_geofence')->default(true);
            $table->boolean('enforce_ip')->default(false);
            $table->text('allowed_ips')->nullable(); // Comma or newline separated
            $table->time('shift_start_time')->default('09:30:00');
            $table->integer('late_grace_minutes')->default(15);
            $table->time('half_day_cutoff_time')->default('13:30:00');
            $table->time('shift_end_time')->default('18:30:00');
            $table->decimal('min_hours_full_day', 4, 2)->default(8.00);
            $table->decimal('min_hours_half_day', 4, 2)->default(4.00);
            $table->json('weekend_days')->nullable(); // e.g. ["Friday", "Saturday"]

            // Dynamic Salary Deduction Configurations
            $table->string('salary_basis', 30)->default('basic_salary'); // basic_salary or gross_salary
            $table->string('salary_day_divisor_mode', 30)->default('actual_working_days'); // actual_working_days, fixed_30_days, calendar_days
            $table->string('late_deduction_mode', 30)->default('quota'); // quota, fixed_amount, percentage, disabled
            $table->integer('late_quota_days')->default(3); // Every X late days
            $table->decimal('late_quota_penalty_days', 3, 2)->default(1.00); // Deduct Y days pay
            $table->decimal('late_fixed_amount', 10, 2)->default(0.00); // Fixed BDT per late
            $table->integer('grace_late_days_per_month')->default(0); // Forgiven late days before penalty kicks in
            $table->decimal('absence_deduction_rate', 3, 2)->default(1.00); // Multiplier per unexcused absence day
            $table->decimal('half_day_deduction_rate', 3, 2)->default(0.50); // Multiplier for half day
            $table->boolean('auto_apply_salary_deduction')->default(true);

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Attendances Table (Daily Records)
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->string('check_in_ip', 45)->nullable();
            $table->string('check_out_ip', 45)->nullable();
            $table->decimal('check_in_lat', 10, 8)->nullable();
            $table->decimal('check_in_lng', 11, 8)->nullable();
            $table->decimal('check_out_lat', 10, 8)->nullable();
            $table->decimal('check_out_lng', 11, 8)->nullable();
            $table->integer('check_in_distance_meters')->nullable();
            $table->integer('check_out_distance_meters')->nullable();
            $table->enum('status', ['present', 'late', 'half_day', 'absent', 'leave'])->default('present');
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->default(0);
            $table->decimal('total_hours', 4, 2)->default(0.00);
            $table->string('device_info', 255)->nullable();
            $table->boolean('is_manually_adjusted')->default(false);
            $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('adjustment_reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index('date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_settings');
    }
};
