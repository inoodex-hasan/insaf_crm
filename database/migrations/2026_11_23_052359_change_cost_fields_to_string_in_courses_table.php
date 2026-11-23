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
            $table->string('tuition_fee', 255)->nullable()->change();
            $table->string('admission_fee', 255)->nullable()->change();
            $table->string('living_cost', 255)->nullable()->change();
            $table->string('additional_cost', 255)->nullable()->change();
            $table->string('scholarship_amount', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('tuition_fee', 10, 2)->nullable()->change();
            $table->decimal('admission_fee', 10, 2)->nullable()->change();
            $table->decimal('living_cost', 10, 2)->nullable()->change();
            $table->decimal('additional_cost', 12, 2)->nullable()->change();
            $table->decimal('scholarship_amount', 12, 2)->nullable()->change();
        });
    }
};
