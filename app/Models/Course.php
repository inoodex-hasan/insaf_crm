<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'university_id',
        'name',
        'description',
        'degree_level',
        'study_area',
        'discipline_area',
        'program_level',
        'duration',
        'duration_type',
        'tuition_fee',
        'fee_period',
        'admission_fee',
        'living_cost',
        'additional_cost',
        'scholarship_amount',
        'currency',
        'requirements',
        'available_intake_months',
        'intake_years',
        'esl_available',
        'stem_program',
        'status',
    ];

    protected $casts = [
        'requirements' => 'array',
        'available_intake_months' => 'array',
        'intake_years' => 'array',
        'esl_available' => 'boolean',
        'stem_program' => 'boolean',
        'status' => 'boolean',
        'tuition_fee' => 'decimal:2',
        'admission_fee' => 'decimal:2',
        'living_cost' => 'decimal:2',
        'additional_cost' => 'decimal:2',
        'scholarship_amount' => 'decimal:2',
    ];

    /**
     * Standard Program Levels matching the reference architecture.
     */
    public const PROGRAM_LEVELS = [
        'high_school' => 'High School (11th - 12th)',
        'ug_diploma' => 'UG Diploma/ Certificate/ Associate Degree',
        'ug' => 'UG (Undergraduate / Bachelor)',
        'pg_diploma' => 'PG Diploma/Certificate',
        'pg' => 'PG (Postgraduate / Master)',
        'accelerated' => 'UG+PG (Accelerated) Degree',
        'phd' => 'PhD (Doctorate)',
        'short_term' => 'Short-term/Summer Programs',
        'pathway_ug' => 'Pathway Programs (UG)',
        'pathway_pg' => 'Pathway Programs (PG)',
        'semester_abroad' => 'Semester Study Abroad',
        'twinning_ug' => 'Twinning Programmes (UG)',
        'twinning_pg' => 'Twinning Programmes (PG)',
    ];

    /**
     * Common Preset Study Areas (Faculties).
     */
    public const COMMON_STUDY_AREAS = [
        'Engineering & Technology',
        'Computer Science & Information Technology',
        'Business, Management & Economics',
        'Medicine, Health & Biomedical Sciences',
        'Law, Criminology & Legal Studies',
        'Natural & Applied Sciences',
        'Arts, Design & Architecture',
        'Social Sciences, Humanities & Media',
        'Hospitality, Tourism & Culinary Arts',
        'Education & Training',
        'Agriculture, Veterinary & Environmental Sciences',
    ];

    /**
     * Standard Requirements & Waivers.
     */
    public const REQUIREMENTS_LIST = [
        'ielts' => 'IELTS',
        'toefl' => 'TOEFL iBT',
        'pte' => 'PTE',
        'det' => 'DET (Duolingo)',
        'sat' => 'SAT',
        'act' => 'ACT',
        'gre' => 'GRE',
        'gmat' => 'GMAT',
        'without_english' => 'Without English Proficiency (MOI)',
        'without_gre' => 'Without GRE',
        'without_gmat' => 'Without GMAT',
        'without_maths' => 'Without Maths',
        'stem' => 'STEM Programs',
    ];

    public static function getProgramLevels(): array
    {
        $levels = ProgramLevel::getActiveLevels();
        return !empty($levels) ? $levels : self::PROGRAM_LEVELS;
    }

    public static function getRequirementsList(): array
    {
        $reqs = ProgramRequirement::getActiveRequirements();
        return !empty($reqs) ? $reqs : self::REQUIREMENTS_LIST;
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function intakes()
    {
        return $this->hasMany(CourseIntake::class);
    }

    public function getProgramLevelLabelAttribute(): string
    {
        if ($this->program_level) {
            $levels = self::getProgramLevels();
            if (isset($levels[$this->program_level])) {
                return $levels[$this->program_level];
            }
        }

        return $this->degree_level ?? 'Degree';
    }

    public function getFormattedFeeAttribute(): string
    {
        if ($this->tuition_fee === null) {
            return 'Contact University';
        }

        $currency = $this->currency ?? 'USD';
        return $currency . ' ' . number_format($this->tuition_fee, 2);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper($this->currency ?? 'USD')) {
            'USD', 'CAD', 'AUD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'BDT' => '৳',
            'MYR' => 'RM ',
            'RUB' => '₽',
            default => ($this->currency ?? '$') . ' ',
        };
    }

    public function getPeriodSuffixAttribute(): string
    {
        $p = strtolower($this->fee_period ?? 'year');
        return match ($p) {
            'year', 'per_year', 'annual' => '/year',
            'semester', 'per_semester', 'sem' => '/sem',
            'month', 'per_month' => '/mo',
            'total', 'course', 'full' => '',
            default => '/' . $p,
        };
    }

    public function getFormattedTuitionDisplayAttribute(): string
    {
        if ($this->tuition_fee === null || (float)$this->tuition_fee <= 0) {
            return 'N/A';
        }
        return $this->currency_symbol . number_format($this->tuition_fee, 0) . $this->period_suffix;
    }

    public function getFormattedAdmissionDisplayAttribute(): string
    {
        if ($this->admission_fee === null || (float)$this->admission_fee <= 0) {
            return 'N/A';
        }
        return $this->currency_symbol . number_format($this->admission_fee, 0);
    }

    public function getFormattedLivingDisplayAttribute(): string
    {
        if ($this->living_cost === null || (float)$this->living_cost <= 0) {
            return 'N/A';
        }
        return $this->currency_symbol . number_format($this->living_cost, 0) . $this->period_suffix;
    }

    public function getFormattedAdditionalDisplayAttribute(): string
    {
        if ($this->additional_cost === null || (float)$this->additional_cost <= 0) {
            return 'N/A';
        }
        return $this->currency_symbol . number_format($this->additional_cost, 0);
    }

    public function getFormattedScholarshipDisplayAttribute(): string
    {
        if ($this->scholarship_amount === null || (float)$this->scholarship_amount <= 0) {
            return 'N/A';
        }
        return $this->currency_symbol . number_format($this->scholarship_amount, 0) . $this->period_suffix;
    }

    public function getNetTuitionFeeAttribute(): float
    {
        return max(0, (float)($this->tuition_fee ?? 0) - (float)($this->scholarship_amount ?? 0));
    }

    public function getTotalEstimatedAnnualCostAttribute(): float
    {
        return $this->net_tuition_fee + (float)($this->admission_fee ?? 0) + (float)($this->living_cost ?? 0) + (float)($this->additional_cost ?? 0);
    }
}
