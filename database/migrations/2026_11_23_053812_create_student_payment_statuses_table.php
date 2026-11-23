<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('student_payment_statuses')) {
            Schema::create('student_payment_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 50)->unique();
                $table->string('color', 30)->default('info');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed initial default statuses
            $defaultStatuses = [
                ['name' => 'Fully Paid', 'code' => 'paid', 'color' => 'success', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Partial Due', 'code' => 'partial', 'color' => 'warning', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Unpaid', 'code' => 'unpaid', 'color' => 'danger', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'No Package Set', 'code' => 'no_package', 'color' => 'secondary', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ];

            DB::table('student_payment_statuses')->insert($defaultStatuses);
        }

        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'payment_status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('payment_status', 50)->nullable()->after('package_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'payment_status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('payment_status');
            });
        }

        Schema::dropIfExists('student_payment_statuses');
    }
};
