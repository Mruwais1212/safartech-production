<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MFrouh\ScopeStatistics\Traits\ScopeStatistics;

class Reservation extends Model
{
    use HasFactory;
    use ScopeStatistics;

    protected $fillable = [
        'user_id',
        'type',
        'trip_id',
        'price',
        'currency',
        'payment_method',
        'status',
        'payment_id',
        'callback_nonce',
        'paid_at',
        'payment_processed_at',
        'booking_in_progress',
        'booking_processing_started_at',
        'booking_completed_at',
        'to_id',
        'to_city',
        'from_id',
        'from_city',
        'uuid',
        'to_id',
        'reservation_type',
        'unit_price',
        'payment_type',
        'search_request',
        'booking_reference_id',
        'booking_error',
        'reconcile_attempt_count',
        'last_reconciled_at',
        'reconciliation_status'
    ];

    protected $casts = [
        'booking_in_progress' => 'boolean',
        'booking_processing_started_at' => 'datetime',
        'booking_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flight()
    {
        return $this->hasMany(ReservationFlight::class, 'reservation_id', 'id');
    }

    public function flights()
    {
        return $this->hasMany(ReservationFlight::class, 'reservation_id', 'id');
    }

    public function hotel()
    {
        return $this->hasOne(ReservationHotel::class, 'reservation_id', 'id');
    }

    public function trip()
    {
        return $this->belongsTo(Destination::class, 'trip_id', 'id');
    }

    public function passengers()
    {
        return $this->hasMany(ReservationPassenger::class);
    }

    public function getType()
    {
        switch ($this->type) {
            case 1:
                return __('dashboard.Flight and Hotel');
            case 2:
                return __('dashboard.Hotel');
            case 3:
                return __('dashboard.Flight');
            default:
                return __('dashboard.Flight and Hotel');
        }
    }

    public function scopeFilterDate($query)
    {
        return $query

            ->when(request()->date == 'today', function ($q) {
                $q->whereDate('created_at', now());
            })

            ->when(request()->date == 'month', function ($q) {
                $q->whereDate('created_at', now()->month);
            })

            ->when(request()->date == 'year', function ($q) {
                $q->whereDate('created_at', now()->year);
            });
    }
}
