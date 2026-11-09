<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'user_id',
        'type',
        'title',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Static helper to log an activity.
     */
    public static function log(
        int $studentId,
        string $type,
        string $title,
        ?string $description = null,
        array $properties = [],
        ?int $userId = null
    ): self {
        return static::create([
            'student_id' => $studentId,
            'user_id' => $userId ?? auth()->id(),
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'properties' => $properties,
        ]);
    }

    /**
     * Get badge color class for activity type.
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'call' => 'bg-info/10 text-info border-info/20',
            'meeting' => 'bg-warning/10 text-warning border-warning/20',
            'note' => 'bg-primary/10 text-primary border-primary/20',
            'status_change' => 'bg-secondary/10 text-secondary border-secondary/20',
            'document' => 'bg-dark/10 text-dark dark:text-white-dark border-dark/20',
            'application' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
            'payment' => 'bg-success/10 text-success border-success/20',
            'vfs' => 'bg-danger/10 text-danger border-danger/20',
            default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        };
    }

    /**
     * Get human label for type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'call' => 'Phone Call',
            'meeting' => 'In-Person Meeting',
            'note' => 'Counselor Note',
            'status_change' => 'Stage / Status Change',
            'document' => 'Document Update',
            'application' => 'Application Milestone',
            'payment' => 'Payment Transaction',
            'vfs' => 'VFS / Visa Update',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}

