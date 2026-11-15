<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostingTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'costing_template_id',
        'category',
        'title',
        'payable_to',
        'currency',
        'default_foreign_amount',
        'default_bdt_amount',
        'is_deduction',
        'is_tuition_placeholder',
        'sort_order',
    ];

    protected $casts = [
        'default_foreign_amount' => 'decimal:2',
        'default_bdt_amount' => 'decimal:2',
        'is_deduction' => 'boolean',
        'is_tuition_placeholder' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CostingTemplate::class, 'costing_template_id');
    }
}
