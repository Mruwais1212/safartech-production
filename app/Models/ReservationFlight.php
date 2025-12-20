<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationFlight extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'result_index',
        'trace_id',
        'is_lcc',
        'is_refundable',
        'last_ticket_date',
        'total_price',
        'is_direct',
        'flight_class',
        'pnr',
        'flight_from',
        'flight_to',
        'departure_time',
        'arrival_time',
        'flight_json',
        'fare_rule_json',
        'fare_quote_json',
        'is_price_changed',
        'result_fare_type',
        'is_pan_required_at_book',
        'is_pan_required_at_ticket',
        'is_passport_required_at_book',
        'is_passport_required_at_ticket',
        'airline_remark',
        'is_bookable_if_seat_not_available',

        'first_tax_rate',
        'first_tax_amount',
        'second_tax_rate',
        'second_tax_amount',
        'administrative_tax_amount',
        'administrative_tax_rate',
        'price_without_tax',
        'price_with_tax',
        'tax_amount',
        'outbound_id',
        'ticket_json',
        'booking_json',

        // InProgress booking status tracking
        'is_in_progress',
        'tbo_booking_id',
        'status',
        
        // Booking details fetching tracking
        'booking_details_fetched',
        'booking_details_fetched_at',
        'booking_details_response',
        'booking_details_fetch_failed',
        'last_fetch_error',
        
        // Status tracking
        'booking_status',
        'ticket_status',
        'booking_status_updated',
        'is_ticketed',
        'ticketed_at',
        
        // Manual review flag
        'needs_manual_review'
    ];

    protected $casts = [
        'is_in_progress' => 'boolean',
        'booking_details_fetched' => 'boolean',
        'booking_details_fetched_at' => 'datetime',
        'booking_details_fetch_failed' => 'boolean',
        'booking_status_updated' => 'boolean',
        'is_ticketed' => 'boolean',
        'ticketed_at' => 'datetime',
        'needs_manual_review' => 'boolean'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function segments()
    {
        return $this->hasMany(FlightSegment::class, 'flight_id', 'id');
    }

    public function getClassFlight()
    {
        if ($this->flight_class == 2) {
            return __('dashboard.Economy');
        }

        if ($this->flight_class == 3) {
            return __('dashboard.PremiumEconomy');
        }

        if ($this->flight_class == 4) {
            return __('dashboard.Business');
        }

        if ($this->flight_class == 5) {
            return __('dashboard.PremiumBusiness');
        }

        if ($this->flight_class == 6) {
            return __('dashboard.First Class');
        }
    }
}
