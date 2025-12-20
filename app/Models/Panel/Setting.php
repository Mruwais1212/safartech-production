<?php

namespace App\Models\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MFrouh\ActivityModel\Interfaces\ActivityInterface;
use MFrouh\ActivityModel\Traits\ActivityModel;

class Setting extends Model implements ActivityInterface
{
    use ActivityModel;
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'name_ar', 'name_en', 'code', 'setting_type_id', 'value',
        'note_ar', 'note_en', 'input_type', 'sort', 'status',
    ];

    public function settingType()
    {
        return $this->belongsTo(SettingType::class, 'setting_type_id', 'id');
    }

    public function activityChanges(): array
    {
        return [];
    }

    public function activityDefault(): array
    {
        $userName = @auth('admin')->user()->name;

        return [
            'created' => [
                'title_ar' => __('activity.setting_title_created', [], 'ar'),
                'title_en' => __('activity.setting_title_created', [], 'en'),
                'message_ar' => __('activity.setting_message_created', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.setting_message_created', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'deleted' => [
                'title_ar' => __('activity.setting_title_deleted', [], 'ar'),
                'title_en' => __('activity.setting_title_deleted', [], 'en'),
                'message_ar' => __('activity.setting_message_deleted', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.setting_message_deleted', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'updated' => [
                'title_ar' => __('activity.setting_title_updated', [], 'ar'),
                'title_en' => __('activity.setting_title_updated', [], 'en'),
                'message_ar' => __('activity.setting_message_updated', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.setting_message_updated', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'restored' => [
                'title_ar' => __('activity.setting_title_restored', [], 'ar'),
                'title_en' => __('activity.setting_title_restored', [], 'en'),
                'message_ar' => __('activity.setting_message_restored', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.setting_message_restored', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
        ];
    }

    public function activityFcmTokens(): array
    {
        return [];
    }
}
