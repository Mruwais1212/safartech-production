<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TBOHotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'address',
        'facilities_en',
        'facilities_ar',
        'images',
        'attractions',
        'latitude',
        'longitude',
        'rating',
        'pin_code',
        'phone',
        'fax',
        'city_code',
        'city_name',
        'country_code',
        'country_name',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'facilities_en' => 'array',
        'facilities_ar' => 'array',
        'images' => 'array',
        'attractions' => 'array',
    ];

    public function scopeFilterByRating($query)
    {
        return $query->when(request()->hotel_rating == 'OneStar', function ($query) {
            $query->where('rating', 1);
        })->when(request()->hotel_rating == 'TwoStar', function ($query) {
            $query->where('rating', 2);
        })->when(request()->hotel_rating == 'ThreeStar', function ($query) {
            $query->where('rating', 3);
        })->when(request()->hotel_rating == 'FourStar', function ($query) {
            $query->where('rating', 4);
        })->when(request()->hotel_rating == 'FiveStar', function ($query) {
            $query->where('rating', 5);
        });
    }
}
