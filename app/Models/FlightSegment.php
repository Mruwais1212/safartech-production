<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSegment extends Model
{
    use HasFactory;

    protected $fillable = ['flight_id', 'baggage', 'cabin_baggage', 'cabin_class', 'trip_indicator', 'segment_indicator', 'duration', 'no_of_seat_available'];

    public function flight()
    {
        return $this->belongsTo(ReservationFlight::class);
    }

    public function origin()
    {
        return $this->hasOne(FlightSegmentOrigin::class, 'flight_segment_id', 'id');
    }

    public function destination()
    {
        return $this->hasOne(FlightSegmentDestination::class, 'flight_segment_id', 'id');
    }

    public function airline()
    {
        return $this->hasOne(FlightSegmentAirline::class, 'flight_segment_id', 'id');
    }
}
