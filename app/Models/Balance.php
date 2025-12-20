<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'title_ar', 'title_en', 'description_ar', 'description_en', 'user_id', 'item_id', 'item_type',
        'balance_type_id', 'amount', 'status', 'currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function balanceType()
    {
        return $this->belongsTo(BalanceType::class);
    }
}
