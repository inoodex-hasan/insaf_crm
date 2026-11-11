<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class University extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'state_province',
        'name',
        'short_name',
        'website',
        'email',
        'phone',
        'address',
        'status'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

