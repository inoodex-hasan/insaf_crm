<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ChartOfAccount;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_investment')->default(false)->after('amount');
            $table->foreignId('staff_id')->nullable()->after('is_investment')->constrained('users')->nullOnDelete();
            $table->foreignId('country_id')->nullable()->after('staff_id')->constrained('countries')->nullOnDelete();
            $table->foreignId('university_id')->nullable()->after('country_id')->constrained('universities')->nullOnDelete();
            $table->string('investment_purpose', 255)->nullable()->after('university_id');
        });

        // Ensure a dedicated Expense Chart of Account exists for Tour & Partnership Investment
        ChartOfAccount::firstOrCreate(
            ['code' => '51006'],
            [
                'name' => 'Tour & Partnership Investment',
                'type' => 'expense',
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['university_id']);
            $table->dropColumn([
                'is_investment',
                'staff_id',
                'country_id',
                'university_id',
                'investment_purpose',
            ]);
        });
    }
};
