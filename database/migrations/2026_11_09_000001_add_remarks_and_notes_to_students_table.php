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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'remarks')) {
                $table->string('remarks')->nullable()->after('package_amount');
            }
            if (!Schema::hasColumn('students', 'notes')) {
                $table->text('notes')->nullable()->after('remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'remarks')) {
                $table->dropColumn('remarks');
            }
            if (Schema::hasColumn('students', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};

