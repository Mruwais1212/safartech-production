<?php

namespace App\Models\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar', 'name_en', 'code', 'status', 'public_key', 'secret_key',
        'merchant_code', 'auth_token', 'tranportal_id', 'tranportal_password', 'resource_key', 'notification_token',
        'currency', 'country_code', 'lang', 'photo',
    ];
}
