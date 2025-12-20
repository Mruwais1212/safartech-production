<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinationTravelInterest extends Model
{
    use HasFactory;

    protected $fillable = ['destination_id', 'travel_interest_id'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function travelInterest()
    {
        return $this->belongsTo(TravelInterest::class);
    }
}
