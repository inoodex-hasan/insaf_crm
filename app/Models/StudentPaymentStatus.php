<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudentPaymentStatus extends Model
{
    use HasFactory;

    protected $table = 'student_payment_statuses';

    protected $fillable = [
        'name',
        'code',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get all active payment statuses.
     */
    public static function getActiveStatuses()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Get CSS badge classes based on color attribute.
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->color) {
            'success' => 'bg-success/10 text-success border border-success/30',
            'danger' => 'bg-danger/10 text-danger border border-danger/30',
            'warning' => 'bg-warning/10 text-warning border border-warning/30',
            'info' => 'bg-info/10 text-info border border-info/30',
            'secondary' => 'bg-secondary/10 text-secondary border border-secondary/30',
            'primary' => 'bg-primary/10 text-primary border border-primary/30',
            default => 'bg-gray-100 text-gray-700 border border-gray-300 dark:bg-gray-800 dark:text-gray-300',
        };
    }

    /**
     * Helper to generate unique slug / code.
     */
    public static function generateCode(string $name): string
    {
        $code = Str::slug($name, '_');
        $original = $code;
        $counter = 1;

        while (static::where('code', $code)->exists()) {
            $code = $original . '_' . $counter;
            $counter++;
        }

        return $code;
    }
}
