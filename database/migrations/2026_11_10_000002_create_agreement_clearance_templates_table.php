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
        Schema::create('agreement_clearance_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->index(); // 'agreement', 'clearance_withdrawal', 'clearance_full'
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('layout_type', 30)->default('pad'); // 'pad', 'stamp'
            $table->string('title');
            $table->longText('content');
            $table->integer('header_space_mm')->default(0); // Top margin clearance for stamp papers (e.g. 90mm)
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_clearance_templates');
    }
};

