<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationHotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'hotel_id',
        'check_in',
        'check_out',
        'rooms',
        'adults',
        'children',
        'price',
        'currency',
        'hotel_name',
        'hotel_image',
        'hotel_address',
        'hotel_rate',
        'booking_code',
        'status',
        'confirmation_number',
        'client_reference_id',
        'booking_status',
        'booking_details_fetched_at',
        'booking_details_response',
        'canceled_at',
        'canceled_by',
        'country_id',
        'city_id',
        'date_from',
        'date_to',

        'first_tax_rate',
        'first_tax_amount',
        'second_tax_rate',
        'second_tax_amount',
        'administrative_tax_amount',
        'administrative_tax_rate',
        'price_without_tax',
        'price_with_tax',
        'tax_amount',
        'room_details',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
