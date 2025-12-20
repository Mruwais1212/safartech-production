<?php

namespace App\Models\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MFrouh\ActivityModel\Interfaces\ActivityInterface;
use MFrouh\ActivityModel\Traits\ActivityModel;

class Slider extends Model implements ActivityInterface
{
    use ActivityModel;
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'main_slider_id', 'photo', 'mobile_photo', 'title_ar', 'title_en', 'text_color',
        'type', 'description_ar', 'description_en', 'button_text_ar', 'button_text_en', 'button_url',
    ];

    public function mainSlider()
    {
        return $this->belongsTo(MainSlider::class, 'main_slider_id', 'id');
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
                'title_ar' => __('activity.slider_title_created', [], 'ar'),
                'title_en' => __('activity.slider_title_created', [], 'en'),
                'message_ar' => __('activity.slider_message_created', ['name' => $this->title_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.slider_message_created', ['name' => $this->title_en, 'user' => $userName], 'en'),
            ],
            'deleted' => [
                'title_ar' => __('activity.slider_title_deleted', [], 'ar'),
                'title_en' => __('activity.slider_title_deleted', [], 'en'),
                'message_ar' => __('activity.slider_message_deleted', ['name' => $this->title_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.slider_message_deleted', ['name' => $this->title_en, 'user' => $userName], 'en'),
            ],
            'updated' => [
                'title_ar' => __('activity.slider_title_updated', [], 'ar'),
                'title_en' => __('activity.slider_title_updated', [], 'en'),
                'message_ar' => __('activity.slider_message_updated', ['name' => $this->title_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.slider_message_updated', ['name' => $this->title_en, 'user' => $userName], 'en'),
            ],
            'restored' => [
                'title_ar' => __('activity.slider_title_restored', [], 'ar'),
                'title_en' => __('activity.slider_title_restored', [], 'en'),
                'message_ar' => __('activity.slider_message_restored', ['name' => $this->title_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.slider_message_restored', ['name' => $this->title_en, 'user' => $userName], 'en'),
            ],
        ];
    }

    public function activityFcmTokens(): array
    {
        return [];
    }
}
