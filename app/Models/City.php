<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['name_ar', 'name_en', 'country_id', 'code'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }
}
