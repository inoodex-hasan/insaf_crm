<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Costing Templates (Presets per country / general)
        Schema::create('costing_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('name');
            $table->string('currency', 10)->default('USD');
            $table->decimal('default_exchange_rate', 10, 2)->default(125.00);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Costing Template Items
        Schema::create('costing_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('costing_template_id')->constrained('costing_templates')->cascadeOnDelete();
            // Category: tuition, scholarship, application_fee, additional_cost, agency_fee
            $table->string('category', 50)->default('additional_cost');
            $table->string('title');
            // Payable to: university, government_embassy, agency, third_party
            $table->string('payable_to', 50)->default('third_party');
            $table->string('currency', 10)->default('BDT');
            $table->decimal('default_foreign_amount', 12, 2)->default(0.00);
            $table->decimal('default_bdt_amount', 12, 2)->default(0.00);
            $table->boolean('is_deduction')->default(false); // true for scholarship/waiver
            $table->boolean('is_tuition_placeholder')->default(false); // dynamically loaded from course
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Student Costings (Customized saved records for students)
        Schema::create('student_costings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('costing_template_id')->nullable()->constrained('costing_templates')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('reference_no')->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->decimal('exchange_rate', 10, 2)->default(1.00);
            
            // Core Mandatory Metric Aggregates
            $table->decimal('tuition_fee', 12, 2)->default(0.00);
            $table->decimal('scholarship_amount', 12, 2)->default(0.00);
            $table->decimal('net_tuition_fee', 12, 2)->default(0.00);
            $table->decimal('application_fee', 12, 2)->default(0.00);
            $table->decimal('total_additional_costs', 12, 2)->default(0.00);
            $table->decimal('agency_fee', 12, 2)->default(0.00);
            $table->decimal('grand_total_bdt', 12, 2)->default(0.00);

            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Student Costing Items (Detailed line items for the student snapshot)
        Schema::create('student_costing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_costing_id')->constrained('student_costings')->cascadeOnDelete();
            $table->string('category', 50)->default('additional_cost');
            $table->string('title');
            $table->string('payable_to', 50)->default('third_party');
            $table->string('currency', 10)->default('BDT');
            $table->decimal('foreign_amount', 12, 2)->default(0.00);
            $table->decimal('exchange_rate', 10, 2)->default(1.00);
            $table->decimal('bdt_amount', 12, 2)->default(0.00);
            $table->boolean('is_deduction')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_costing_items');
        Schema::dropIfExists('student_costings');
        Schema::dropIfExists('costing_template_items');
        Schema::dropIfExists('costing_templates');
    }
};
