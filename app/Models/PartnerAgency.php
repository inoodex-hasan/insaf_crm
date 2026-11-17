<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerAgency extends Model
{
    use HasFactory;

    protected $table = 'partner_agencies';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'specialized_countries',
        'commission_terms',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function commissions()
    {
        return $this->hasMany(PartnerCommission::class, 'partner_agency_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
