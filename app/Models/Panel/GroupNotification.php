<?php

namespace App\Models\Panel;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupNotification extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'title_ar', 'title_en', 'message_ar', 'message_en', 'types'];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }
}
