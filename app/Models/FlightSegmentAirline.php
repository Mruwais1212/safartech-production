<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSegmentAirline extends Model
{
    use HasFactory;

    protected $fillable = ['flight_segment_id', 'airline_code', 'airline_name', 'flight_number', 'fare_class'];

    public function segment()
    {
        return $this->belongsTo(FlightSegment::class);
    }
}
