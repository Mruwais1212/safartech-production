<?php

namespace App\Models\Panel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MFrouh\ActivityModel\Interfaces\ActivityInterface;
use MFrouh\ActivityModel\Traits\ActivityModel;

class MainSlider extends Model implements ActivityInterface
{
    use ActivityModel;
    use HasFactory;

    protected $table = 'main_sliders';

    protected $guarded = ['id'];

    public function sliders()
    {
        return $this->hasMany(Slider::class);
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
                'title_ar' => __('activity.main_slider_title_created', [], 'ar'),
                'title_en' => __('activity.main_slider_title_created', [], 'en'),
                'message_ar' => __('activity.main_slider_message_created', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.main_slider_message_created', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'deleted' => [
                'title_ar' => __('activity.main_slider_title_deleted', [], 'ar'),
                'title_en' => __('activity.main_slider_title_deleted', [], 'en'),
                'message_ar' => __('activity.main_slider_message_deleted', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.main_slider_message_deleted', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'updated' => [
                'title_ar' => __('activity.main_slider_title_updated', [], 'ar'),
                'title_en' => __('activity.main_slider_title_updated', [], 'en'),
                'message_ar' => __('activity.main_slider_message_updated', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.main_slider_message_updated', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
            'restored' => [
                'title_ar' => __('activity.main_slider_title_restored', [], 'ar'),
                'title_en' => __('activity.main_slider_title_restored', [], 'en'),
                'message_ar' => __('activity.main_slider_message_restored', ['name' => $this->name_ar, 'user' => $userName], 'ar'),
                'message_en' => __('activity.main_slider_message_restored', ['name' => $this->name_en, 'user' => $userName], 'en'),
            ],
        ];
    }

    public function activityFcmTokens(): array
    {
        return [];
    }
}
