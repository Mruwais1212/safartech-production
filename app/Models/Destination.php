<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Destination extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'description_ar',
        'description_en',
        'country_id',
        'city_id',
        'estimated_cost',
        'latitude',
        'longitude',
        'image',
        'status',
    ];

    protected $appends = ['price', 'currency'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function travelInterests()
    {
        return $this->belongsToMany(TravelInterest::class, 'destination_travel_interests');
    }

    public function getPriceAttribute()
    {
        $exchange_rates = Cache::remember('exchange_rates', 60 * 60 * 24, function () {
            $api_key = config('services.exchange_rate.api_key');

            return @Http::get("https://v6.exchangerate-api.com/v6/$api_key/latest/USD")->json('conversion_rates')['SAR'] ?? 3.75;
        });

        $currency = session('currency') ?? config('app.currency');

        return $currency == 'SAR' ? $this->estimated_cost * $exchange_rates : $this->estimated_cost;
    }

    public function getCurrencyAttribute()
    {
        return session('currency') == 'SAR' ? __('dashboard.sar') : __('dashboard.dollar');
    }
}
