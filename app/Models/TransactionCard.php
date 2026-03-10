<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCard extends Model
{
    protected $fillable = [
        'number',
        'name',
        'reference_number',
        'company',
        'reservation_id',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
