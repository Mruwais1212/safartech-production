<?php

namespace App\Models\Panel;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MFrouh\ActivityModel\Interfaces\ActivityInterface;
use MFrouh\ActivityModel\Traits\ActivityModel;

class PrivilegeGroup extends Model implements ActivityInterface
{
    use ActivityModel;
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['name_ar', 'name_en', 'guard', 'provider_id'];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    public function privileges()
    {
        return $this->belongsToMany(Privilege::class, 'group_privileges', 'privilege_group_id', 'privilege_id')->where('status', 1);
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
                'title_ar' => __('activity.privilege_group_title_created', [], 'ar'),
                'title_en' => __('activity.privilege_group_title_created', [], 'en'),
                'message_ar' => __('activity.privilege_group_message_created', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_group_message_created', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'deleted' => [
                'title_ar' => __('activity.privilege_group_title_deleted', [], 'ar'),
                'title_en' => __('activity.privilege_group_title_deleted', [], 'en'),
                'message_ar' => __('activity.privilege_group_message_deleted', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_group_message_deleted', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'updated' => [
                'title_ar' => __('activity.privilege_group_title_updated', [], 'ar'),
                'title_en' => __('activity.privilege_group_title_updated', [], 'en'),
                'message_ar' => __('activity.privilege_group_message_updated', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_group_message_updated', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'restored' => [
                'title_ar' => __('activity.privilege_group_title_restored', [], 'ar'),
                'title_en' => __('activity.privilege_group_title_restored', [], 'en'),
                'message_ar' => __('activity.privilege_group_message_restored', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.privilege_group_message_restored', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
        ];
    }

    public function activityFcmTokens(): array
    {
        return [];
    }
}
