<?php

namespace App\Models;

use App\Models\Panel\DeviceToken;
use App\Models\Panel\GroupPrivileges;
use App\Models\Panel\Notification;
use App\Models\Panel\Privilege;
use App\Models\Panel\PrivilegeGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use MFrouh\ScopeStatistics\Traits\ScopeStatistics;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use ScopeStatistics;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'phone_code', 'photo', 'activate', 'activation_code', 'token',
        'user_type_id', 'receive_notification', 'lang', 'last_login', 'group_privilege_id', 'block', 'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function routeNotificationForFcm()
    {
        return $this->id;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function privileges()
    {
        return $this->hasManyThrough(Privilege::class, GroupPrivileges::class, 'privilege_group_id', 'id', 'group_privilege_id', 'privilege_id');
    }

    public function privilegeGroup()
    {
        return $this->belongsTo(PrivilegeGroup::class, 'group_privilege_id', 'id');
    }

    public function checkHasPermission($permission)
    {
        return $this->privileges()->where('code', $permission)->first() ? true : false;
    }

    public function balances()
    {
        return $this->hasMany(Balance::class, 'user_id', 'id')->where('status', 1);
    }

    public function blockedBalances()
    {
        return $this->hasMany(Balance::class, 'user_id', 'id')->where('status', 0);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'user_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
