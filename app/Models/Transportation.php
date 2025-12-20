<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar', 'name_en', 'min_price', 'max_price', 'destination_id',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
