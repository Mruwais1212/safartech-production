<?php

namespace App\Models\Panel;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MFrouh\ActivityModel\Interfaces\ActivityInterface;
use MFrouh\ActivityModel\Traits\ActivityModel;

class Privilege extends Model implements ActivityInterface
{
    use ActivityModel;
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name_ar', 'name_en', 'url', 'controller', 'method', 'type', 'icon',
        'parent_id', 'sort', 'status', 'code', 'guard',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 1);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subPrivilege()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function subPrivilegeWithPermission()
    {
        $guard = [];
        switch (auth()->user()->user_type_id) {
            case UserType::SUPER_ADMIN:
                $guard = [1, 2];

                break;

            case UserType::ADMIN:
                $guard = [1];

                break;
            default:
                $guard = [1];

                break;
        }

        return $this->hasMany(self::class, 'parent_id')
            ->whereIn('id', auth()->user()->privileges()->pluck('privilege_id')->toArray())
            ->where('status', 1)
            ->where('type', 1)
            ->whereIn('guard', $guard);
    }

    public function checkRequest()
    {
        if ($this->url) {
            return ltrim($this->url, '/') == ltrim(request()->getRequestUri(), '/en/') ? true : false;
        }

        return false;
    }

    public function groups()
    {
        return $this->belongsToMany(PrivilegeGroup::class, 'group_privileges', 'privilege_id', 'privilege_group_id')
            ->where('status', 1);
    }

    public function classCss()
    {
        $active = '';
        foreach ($this->subPrivilegeWithPermission as $subPrivilege) {
            $active = $subPrivilege->checkRequest() ? ' active' : '';
            if ($active != '') {
                break;
            }
        }

        if ($this->subPrivilegeWithPermission->count() != 0) {
            $active .= ' side_list';
        }

        if ($this->checkRequest()) {
            $active .= ' active';
        }

        return $active;
    }

    public function classSubPrivilegeCss()
    {
        $active = '';
        if ($this->checkRequest()) {
            $active .= ' active';
        }

        return $active;
    }

    public function activityChanges(): array
    {
        return [];
    }

    public function activityDefault(): array
    {
        $userName = auth('admin')->user()->name;

        return [
            'created' => [
                'title_ar' => __('activity.privilege_title_created', [], 'ar'),
                'title_en' => __('activity.privilege_title_created', [], 'en'),
                'message_ar' => __('activity.privilege_message_created', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_message_created', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'deleted' => [
                'title_ar' => __('activity.privilege_title_deleted', [], 'ar'),
                'title_en' => __('activity.privilege_title_deleted', [], 'en'),
                'message_ar' => __('activity.privilege_message_deleted', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_message_deleted', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'updated' => [
                'title_ar' => __('activity.privilege_title_updated', [], 'ar'),
                'title_en' => __('activity.privilege_title_updated', [], 'en'),
                'message_ar' => __('activity.privilege_message_updated', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_message_updated', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'restored' => [
                'title_ar' => __('activity.privilege_title_restored', [], 'ar'),
                'title_en' => __('activity.privilege_title_restored', [], 'en'),
                'message_ar' => __('activity.privilege_message_restored', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_message_restored', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
        ];
    }

    public function activityFcmTokens(): array
    {
        return [];
    }
}
