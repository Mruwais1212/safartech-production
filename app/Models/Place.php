<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'destination_id',
        'image',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
