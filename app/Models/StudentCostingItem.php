<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCostingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_costing_id',
        'category',
        'title',
        'payable_to',
        'currency',
        'foreign_amount',
        'exchange_rate',
        'bdt_amount',
        'is_deduction',
        'sort_order',
    ];

    protected $casts = [
        'foreign_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:2',
        'bdt_amount' => 'decimal:2',
        'is_deduction' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function studentCosting(): BelongsTo
    {
        return $this->belongsTo(StudentCosting::class, 'student_costing_id');
    }
}
