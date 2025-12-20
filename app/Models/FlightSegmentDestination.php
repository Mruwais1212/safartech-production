<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSegmentDestination extends Model
{
    use HasFactory;

    protected $fillable = ['flight_segment_id', 'airport_code', 'airport_name', 'city_code', 'city_name', 'country_code', 'country_name', 'arr_time'];

    public function segment()
    {
        return $this->belongsTo(FlightSegment::class);
    }
}
