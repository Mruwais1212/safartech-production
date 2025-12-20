<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'title',
        'first_name',
        'last_name',
        'pax_type',
        'birth_date',
        'email',
        'phone',
        'nationality',
        'gender',
        'address',
        'passport_number',
        'passport_expiry_date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getAgeAttribute()
    {
        $birthDate = new \DateTime($this->birth_date);
        $now = new \DateTime;
        $interval = $now->diff($birthDate);

        return $interval->y;
    }

    public function getNationality()
    {
        return $this->belongsTo(Country::class, 'nationality', 'code');
    }
}
