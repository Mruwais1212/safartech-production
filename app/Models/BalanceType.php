<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceType extends Model
{
    use HasFactory;

    protected $fillable = ['title_ar', 'title_en'];

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }
}
