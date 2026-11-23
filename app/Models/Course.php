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

    public function getFormattedDurationAttribute(): string
    {
        if (!empty($this->duration)) {
            $dur = trim($this->duration);
            if (preg_match('/[a-zA-Z]/', $dur)) {
                return $dur;
            }
            if (is_numeric($dur)) {
                $num = (int)$dur;
                if ($num <= 5) {
                    return $num . ($num == 1 ? ' Year' : ' Years');
                }
                return $num . ' Months';
            }
            return $dur;
        }

        if (!empty($this->duration_type)) {
            return match ($this->duration_type) {
                'less_than_1_year' => '< 1 Year',
                '1_2_years' => '1 - 2 Years',
                '3_4_years' => '3 - 4 Years',
                '4_plus_years' => '4+ Years',
                default => str_replace('_', ' ', $this->duration_type),
            };
        }

        return 'N/A';
    }

    public function getFormattedFeeAttribute(): string
    {
        if ($this->tuition_fee === null || trim((string)$this->tuition_fee) === '') {
            return 'Contact University';
        }

        $val = trim((string)$this->tuition_fee);
        if (is_numeric($val)) {
            $currency = $this->currency ?? 'USD';
            return $currency . ' ' . number_format((float)$val, 2);
        }

        return $val;
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
        if ($this->tuition_fee === null || trim((string)$this->tuition_fee) === '') {
            return 'N/A';
        }
        $val = trim((string)$this->tuition_fee);
        if (is_numeric($val)) {
            return (float)$val > 0 ? $this->currency_symbol . number_format((float)$val, 0) . $this->period_suffix : 'N/A';
        }
        return $val;
    }

    public function getFormattedAdmissionDisplayAttribute(): string
    {
        if ($this->admission_fee === null || trim((string)$this->admission_fee) === '') {
            return 'N/A';
        }
        $val = trim((string)$this->admission_fee);
        if (is_numeric($val)) {
            return (float)$val > 0 ? $this->currency_symbol . number_format((float)$val, 0) : 'N/A';
        }
        return $val;
    }

    public function getFormattedLivingDisplayAttribute(): string
    {
        if ($this->living_cost === null || trim((string)$this->living_cost) === '') {
            return 'N/A';
        }
        $val = trim((string)$this->living_cost);
        if (is_numeric($val)) {
            return (float)$val > 0 ? $this->currency_symbol . number_format((float)$val, 0) . $this->period_suffix : 'N/A';
        }
        return $val;
    }

    public function getFormattedAdditionalDisplayAttribute(): string
    {
        if ($this->additional_cost === null || trim((string)$this->additional_cost) === '') {
            return 'N/A';
        }
        $val = trim((string)$this->additional_cost);
        if (is_numeric($val)) {
            return (float)$val > 0 ? $this->currency_symbol . number_format((float)$val, 0) : 'N/A';
        }
        return $val;
    }

    public function getFormattedScholarshipDisplayAttribute(): string
    {
        if ($this->scholarship_amount === null || trim((string)$this->scholarship_amount) === '') {
            return 'N/A';
        }
        $val = trim((string)$this->scholarship_amount);
        if (is_numeric($val)) {
            return (float)$val > 0 ? $this->currency_symbol . number_format((float)$val, 0) . $this->period_suffix : 'N/A';
        }
        return $val;
    }

    protected function parseCostValue($value): float
    {
        if ($value === null || trim((string)$value) === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        if (preg_match('/[0-9]+(?:\.[0-9]+)?/', str_replace(',', '', (string)$value), $matches)) {
            return (float)$matches[0];
        }
        return 0.0;
    }

    public function getNetTuitionFeeAttribute(): float
    {
        return max(0, $this->parseCostValue($this->tuition_fee) - $this->parseCostValue($this->scholarship_amount));
    }

    public function getTotalEstimatedAnnualCostAttribute(): float
    {
        return $this->net_tuition_fee 
            + $this->parseCostValue($this->admission_fee) 
            + $this->parseCostValue($this->living_cost) 
            + $this->parseCostValue($this->additional_cost);
    }
}
