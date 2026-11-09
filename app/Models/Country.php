<?php

namespace App\Models;

use App\Models\University;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status'
    ];

    public function universities()
    {
        return $this->hasMany(University::class);
    }

    public function remarks()
    {
        return $this->hasMany(CountryRemark::class)->ordered();
    }
}
