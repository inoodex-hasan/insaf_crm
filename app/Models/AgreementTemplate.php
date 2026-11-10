<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementTemplate extends Model
{
    use HasFactory;

    protected $table = 'agreement_clearance_templates';

    protected $fillable = [
        'type',
        'country_id',
        'layout_type',
        'title',
        'content',
        'header_space_mm',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'header_space_mm' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAgreements(Builder $query): Builder
    {
        return $query->where('type', 'agreement');
    }

    public function scopeClearances(Builder $query): Builder
    {
        return $query->whereIn('type', ['clearance_withdrawal', 'clearance_full']);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Helper to render type display name.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'agreement' => 'Student Agreement',
            'clearance_withdrawal' => 'Withdrawal File Clearance',
            'clearance_full' => 'Full Clearance (All Done)',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}

