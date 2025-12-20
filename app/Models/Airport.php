<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'country_code',
        'city',
        'city_code',
        'airport',
        'airport_code',
    ];
}
