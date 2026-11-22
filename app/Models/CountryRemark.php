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
     * Default pipeline remarks fallback for destination countries when database is not yet seeded.
     */
    public static function getDefaultFallbackRemarks(): array
    {
        return [
            'malta' => [
                'Offer Letter Pending',
                'Application Pending',
                'Slot Pending',
                'Submission Pending',
                'Visa Approval',
                'Visa Refused',
                'Flight Done',
            ],
            'malaysia' => [
                'Pending Apply',
                'Pending OL',
                'Pending EMGS',
                'EMGS - 5%',
                'EMGS - 15%',
                'EMGS - 32%',
                'EMGS - 35%',
                'EMGS - 70%',
                'Visa Apply',
                'Interview',
                'Visa Approved',
                'Visa Reject',
                'Flight Done',
            ],
            'russia' => [
                'Translation Pending',
                'Application Done',
                'Waiting for OL',
                'Waiting for Invitation',
                'Waiting for Submission',
                'Visa Approved',
                'Visa Reject',
                'Flight Done',
            ],
            'south korea' => [
                'Pending Apply',
                'Pending OL',
                'Apostille Pending',
                'Waiting for Visa Issuance',
                'Visa Approved',
                'Visa Reject',
                'Flight Done',
            ],
            'others' => [
                'Pending Application',
                'OL Received',
                'OL Pending',
                'Embassy Submission',
                'Visa Approved',
                'Visa Reject',
                'Flight Done',
            ],
        ];
    }

    /**
     * Get all unique remarks across all countries and fallbacks.
     */
    public static function getAllDistinctRemarks(): array
    {
        $dbRemarks = static::active()->ordered()->pluck('name', 'name')->toArray();
        if (!empty($dbRemarks)) {
            return $dbRemarks;
        }

        $all = [];
        foreach (static::getDefaultFallbackRemarks() as $list) {
            foreach ($list as $r) {
                $all[$r] = $r;
            }
        }
        return $all;
    }

    /**
     * Get remarks options key-value array for a specific country id or country name.
     * Falls back to default remarks (country_id = null) or hardcoded defaults if database is empty.
     */
    public static function getRemarksForCountry(?int $countryId = null, ?string $countryName = null): array
    {
        // 1. Resolve country if only one identifier is provided
        if (!$countryId && $countryName) {
            $country = Country::whereRaw('LOWER(name) = ?', [strtolower(trim($countryName))])->first();
            $countryId = $country?->id;
        } elseif ($countryId && !$countryName) {
            $country = Country::find($countryId);
            $countryName = $country?->name;
        }

        // 2. Query remarks for the specific country if found in database
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

        // 3. Fallback to default remarks from database (where country_id is null)
        $dbDefaults = static::active()
            ->whereNull('country_id')
            ->ordered()
            ->pluck('name', 'name')
            ->toArray();

        if (!empty($dbDefaults)) {
            return $dbDefaults;
        }

        // 4. Code-level fallback if database has not been seeded yet
        $defaults = static::getDefaultFallbackRemarks();
        if ($countryName) {
            $cKey = strtolower(trim($countryName));
            if (isset($defaults[$cKey])) {
                return array_combine($defaults[$cKey], $defaults[$cKey]);
            }
        }

        return array_combine($defaults['others'], $defaults['others']);
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

        // Merge default fallbacks if database table is unseeded
        if (empty($grouped['others']) && count($grouped) <= 1) {
            foreach (static::getDefaultFallbackRemarks() as $cKey => $list) {
                foreach ($list as $rName) {
                    $grouped[$cKey][$rName] = $rName;
                }
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

