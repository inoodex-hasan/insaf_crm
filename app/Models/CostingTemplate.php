<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostingTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'currency',
        'default_exchange_rate',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'default_exchange_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CostingTemplateItem::class)->orderBy('sort_order');
    }

    public function studentCostings(): HasMany
    {
        return $this->hasMany(StudentCosting::class);
    }
}
