<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $table = 'tours';

    protected $fillable = [
        'user_id',
        'tour_data',
        'city',
        'date_from',
        'date_to',
        'come_with',
    ];
}
