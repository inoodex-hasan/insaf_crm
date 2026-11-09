<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryRemark extends Model
{
    use HasFactory;

    protected $table = 'country_remarks';

    protected $fillable = [
        'country_id',
        'name',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Associated country. Null indicates default remark applicable to any country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Scope for active remarks.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by sort_order then id.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Get remarks options key-value array for a specific country id or country name.
     * Falls back to default remarks (country_id = null) or config file if empty.
     */
    public static function getRemarksForCountry(?int $countryId = null, ?string $countryName = null): array
    {
        // 1. Resolve country_id if only name is provided
        if (!$countryId && $countryName) {
            $country = Country::whereRaw('LOWER(name) = ?', [strtolower(trim($countryName))])->first();
            $countryId = $country?->id;
        }

        // 2. Query remarks for the specific country if found
        if ($countryId) {
            $remarks = static::active()
                ->where('country_id', $countryId)
                ->ordered()
                ->pluck('name', 'name')
                ->toArray();

            if (!empty($remarks)) {
                return $remarks;
            }
        }

        // 3. Fallback to default remarks (where country_id is null)
        return static::active()
            ->whereNull('country_id')
            ->ordered()
            ->pluck('name', 'name')
            ->toArray();
    }

    /**
     * Get all country remarks grouped by country key for fast frontend caching.
     */
    public static function getAllGroupedRemarks(): array
    {
        $all = static::with('country')->active()->ordered()->get();
        $grouped = [
            'others' => [],
        ];

        foreach ($all as $remark) {
            if ($remark->country_id && $remark->country) {
                $key = strtolower(trim($remark->country->name));
                $grouped[$key][$remark->name] = $remark->name;
            } else {
                $grouped['others'][$remark->name] = $remark->name;
            }
        }

        return $grouped;
    }

    /**
     * Helper to get CSS badge class for color attribute.
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->color) {
            'success' => 'bg-success/10 text-success border border-success/30',
            'danger' => 'bg-danger/10 text-danger border border-danger/30',
            'warning' => 'bg-warning/10 text-warning border border-warning/30',
            'info' => 'bg-info/10 text-info border border-info/30',
            'secondary' => 'bg-secondary/10 text-secondary border border-secondary/30',
            'dark' => 'bg-dark/10 text-dark border border-dark/30',
            default => 'bg-primary/10 text-primary border border-primary/30',
        };
    }
}

