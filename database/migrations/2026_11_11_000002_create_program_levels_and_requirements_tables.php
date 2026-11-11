<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('program_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category')->default('test'); // test, waiver, other
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default program levels matching reference screenshot
        $defaultLevels = [
            ['name' => 'High School (11th - 12th)', 'code' => 'high_school', 'sort_order' => 1],
            ['name' => 'UG Diploma/ Certificate/ Associate Degree', 'code' => 'ug_diploma', 'sort_order' => 2],
            ['name' => 'UG', 'code' => 'ug', 'sort_order' => 3],
            ['name' => 'PG Diploma/Certificate', 'code' => 'pg_diploma', 'sort_order' => 4],
            ['name' => 'PG', 'code' => 'pg', 'sort_order' => 5],
            ['name' => 'UG+PG (Accelerated) Degree', 'code' => 'accelerated', 'sort_order' => 6],
            ['name' => 'PhD', 'code' => 'phd', 'sort_order' => 7],
            ['name' => 'Short-term/Summer Programs', 'code' => 'short_term', 'sort_order' => 8],
            ['name' => 'Pathway Programs (UG)', 'code' => 'pathway_ug', 'sort_order' => 9],
            ['name' => 'Pathway Programs (PG)', 'code' => 'pathway_pg', 'sort_order' => 10],
            ['name' => 'Semester Study Abroad', 'code' => 'semester_abroad', 'sort_order' => 11],
            ['name' => 'Twinning Programmes (UG)', 'code' => 'twinning_ug', 'sort_order' => 12],
            ['name' => 'Twinning Programmes (PG)', 'code' => 'twinning_pg', 'sort_order' => 13],
        ];

        foreach ($defaultLevels as $lvl) {
            DB::table('program_levels')->insert(array_merge($lvl, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Seed default requirements matching reference screenshot
        $defaultRequirements = [
            ['name' => 'PTE', 'code' => 'pte', 'category' => 'test', 'sort_order' => 1],
            ['name' => 'TOEFL iBT', 'code' => 'toefl_ibt', 'category' => 'test', 'sort_order' => 2],
            ['name' => 'IELTS', 'code' => 'ielts', 'category' => 'test', 'sort_order' => 3],
            ['name' => 'DET (Duolingo)', 'code' => 'det', 'category' => 'test', 'sort_order' => 4],
            ['name' => 'SAT', 'code' => 'sat', 'category' => 'test', 'sort_order' => 5],
            ['name' => 'ACT', 'code' => 'act', 'category' => 'test', 'sort_order' => 6],
            ['name' => 'GRE', 'code' => 'gre', 'category' => 'test', 'sort_order' => 7],
            ['name' => 'GMAT', 'code' => 'gmat', 'category' => 'test', 'sort_order' => 8],
            ['name' => 'Without English Proficiency', 'code' => 'without_english', 'category' => 'waiver', 'sort_order' => 9],
            ['name' => 'Without GRE', 'code' => 'without_gre', 'category' => 'waiver', 'sort_order' => 10],
            ['name' => 'Without GMAT', 'code' => 'without_gmat', 'category' => 'waiver', 'sort_order' => 11],
            ['name' => 'Without Maths', 'code' => 'without_maths', 'category' => 'waiver', 'sort_order' => 12],
        ];

        foreach ($defaultRequirements as $req) {
            DB::table('program_requirements')->insert(array_merge($req, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('program_requirements');
        Schema::dropIfExists('program_levels');
    }
};

