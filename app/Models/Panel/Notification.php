<?php

namespace App\Models\Panel;

use App\Http\Resources\UserInformationResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'title_ar', 'title_en', 'message_ar', 'message_en', 'status', 'type',
        'user_type_id', 'item_id', 'item_type', 'notification_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function item()
    {
        if ($this->item_type == 'user') {
            return $this->belongsTo(User::class, 'item_id', 'id');
        }

        return null;
    }

    public function itemResource()
    {
        if ($this->item_type == 'user') {
            return UserInformationResource::make($this->item);
        }
    }
}
