<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use HasinHayder\Tyro\Concerns\HasTyroRoles;
use HasinHayder\TyroLogin\Traits\HasTwoFactorAuth;



class User extends Authenticatable
{
    use HasApiTokens, HasTyroRoles, HasTwoFactorAuth;


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'plain_password',
        'designation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'plain_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is a senior.
     */
    public function isSenior(): bool
    {
        return $this->designation === 'senior';
    }

    /**
     * Check if the user is a super admin.
     * Only true super-admin role is exempt; all other users (including admin) must track attendance.
     */
    public function isSuperAdmin(): bool
    {
        return method_exists($this, 'hasRole') && ($this->hasRole('super-admin') || $this->hasRole('superadmin'));
    }

    /**
     * Check if user is super admin or admin (administrator).
     * Grants management access to Attendance Board, Reports, and Settings.
     */
    public function isAdminOrSuperAdmin(): bool
    {
        return method_exists($this, 'hasRole') && (
            $this->hasRole('super-admin') ||
            $this->hasRole('superadmin') ||
            $this->hasRole('admin') ||
            $this->hasRole('administrator')
        );
    }

    /**
     * Attendance relationships
     */
    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function todayAttendance(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Attendance::class)->where('date', now()->toDateString());
    }
}
