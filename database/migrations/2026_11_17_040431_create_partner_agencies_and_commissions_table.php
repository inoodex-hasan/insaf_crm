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
        // 1. Partner Agencies (For B2B agency referral & handover)
        Schema::create('partner_agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('specialized_countries')->nullable(); // e.g. "China, South Korea, Japan"
            $table->text('commission_terms')->nullable();
            $table->tinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // 2. Partner Commissions (University & Agency Commissions)
        Schema::create('partner_commissions', function (Blueprint $table) {
            $table->id();
            $table->string('commission_type', 20); // 'university' or 'agency'
            $table->string('direction', 20); // 'receivable' (incoming to Insaf) or 'payable' (outgoing from Insaf)
            $table->string('agency_referral_type', 30)->nullable(); // 'outbound_handover' or 'inbound_referral'
            
            $table->unsignedBigInteger('partner_agency_id')->nullable();
            $table->unsignedBigInteger('university_id')->nullable();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('handover_officer_id')->nullable(); // Staff who managed file

            $table->string('rate_type', 20)->default('fixed'); // 'percentage' or 'fixed'
            $table->decimal('rate_value', 12, 2)->default(0.00); // % value or fixed amount
            $table->decimal('base_amount', 14, 2)->nullable(); // Tuition fee or gross amount
            $table->string('currency', 10)->default('BDT'); // USD, EUR, RMB, BDT etc.
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('total_commission', 14, 2)->default(0.00); // Commission in original currency
            $table->decimal('total_amount_bdt', 14, 2)->default(0.00); // Converted to BDT
            $table->decimal('paid_amount_bdt', 14, 2)->default(0.00);
            $table->decimal('due_amount_bdt', 14, 2)->default(0.00);

            $table->string('workflow_status', 30)->default('pending'); // 'pending', 'processing', 'visa_approved', 'settled', 'cancelled'
            $table->string('payment_status', 30)->default('unpaid'); // 'unpaid', 'partial', 'paid'
            $table->date('handover_date')->nullable();
            $table->string('processing_stage', 100)->nullable(); // e.g. 'Handed Over to Agency', 'Documents Verification', 'Offer Letter Issued', 'Visa Approved', 'Completed'
            $table->string('agreement_reference', 100)->nullable();
            $table->text('processing_notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('partner_agency_id')->references('id')->on('partner_agencies')->nullOnDelete();
            $table->foreign('university_id')->references('id')->on('universities')->nullOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('application_id')->references('id')->on('applications')->nullOnDelete();
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('handover_officer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // 3. Partner Commission Settlements (Linked to Office Accounts & Double-entry Ledger)
        Schema::create('partner_commission_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_commission_id');
            $table->unsignedBigInteger('office_account_id');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->string('settlement_type', 20); // 'received' (inflow) or 'paid' (outflow)
            $table->decimal('amount_bdt', 14, 2);
            $table->string('payment_method', 50)->default('bank_transfer');
            $table->string('reference_no', 100)->nullable();
            $table->date('settlement_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('partner_commission_id')->references('id')->on('partner_commissions')->cascadeOnDelete();
            $table->foreign('office_account_id')->references('id')->on('office_accounts')->cascadeOnDelete();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Seed Chart of Accounts for Partner Commission heads if not exists
        $coaIncome = DB::table('chart_of_accounts')->where('code', '41002')->first();
        if (!$coaIncome) {
            DB::table('chart_of_accounts')->insert([
                'code' => '41002',
                'name' => 'Partner & University Commission Income',
                'type' => 'revenue',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $coaExpense = DB::table('chart_of_accounts')->where('code', '51007')->first();
        if (!$coaExpense) {
            DB::table('chart_of_accounts')->insert([
                'code' => '51007',
                'name' => 'Partner Agency Commission Expense',
                'type' => 'expense',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_commission_settlements');
        Schema::dropIfExists('partner_commissions');
        Schema::dropIfExists('partner_agencies');
    }
};
