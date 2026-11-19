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
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'fee_period')) {
                $table->string('fee_period', 30)->default('year')->after('tuition_fee');
            }
            if (!Schema::hasColumn('courses', 'admission_fee')) {
                $table->decimal('admission_fee', 12, 2)->nullable()->default(0.00)->after('fee_period');
            }
            if (!Schema::hasColumn('courses', 'living_cost')) {
                $table->decimal('living_cost', 12, 2)->nullable()->default(0.00)->after('admission_fee');
            }
            if (!Schema::hasColumn('courses', 'additional_cost')) {
                $table->decimal('additional_cost', 12, 2)->nullable()->default(0.00)->after('living_cost');
            }
            if (!Schema::hasColumn('courses', 'scholarship_amount')) {
                $table->decimal('scholarship_amount', 12, 2)->nullable()->default(0.00)->after('additional_cost');
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
                'fee_period',
                'admission_fee',
                'living_cost',
                'additional_cost',
                'scholarship_amount',
            ]);
        });
    }
};
