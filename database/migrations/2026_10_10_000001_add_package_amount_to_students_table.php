<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'package_amount')) {
            Schema::table('students', function (Blueprint $table) {
                $table->decimal('package_amount', 12, 2)->nullable()->after('subject');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'package_amount')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('package_amount');
            });
        }
    }
};
