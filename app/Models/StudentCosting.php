<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'university_id',
        'country_id',
        'costing_template_id',
        'created_by',
        'reference_no',
        'currency',
        'exchange_rate',
        'tuition_fee',
        'scholarship_amount',
        'net_tuition_fee',
        'application_fee',
        'total_additional_costs',
        'agency_fee',
        'grand_total_bdt',
        'notes',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:2',
        'tuition_fee' => 'decimal:2',
        'scholarship_amount' => 'decimal:2',
        'net_tuition_fee' => 'decimal:2',
        'application_fee' => 'decimal:2',
        'total_additional_costs' => 'decimal:2',
        'agency_fee' => 'decimal:2',
        'grand_total_bdt' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CostingTemplate::class, 'costing_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StudentCostingItem::class)->orderBy('sort_order');
    }
}
