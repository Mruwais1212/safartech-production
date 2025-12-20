<?php

namespace App\Http\Requests\Panel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GroupNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title_ar' => ['required', 'string'],
            'title_en' => ['required', 'string'],
            'message_ar' => ['required', 'string'],
            'message_en' => ['required', 'string'],
        ];
    }

    public function attributes()
    {
        return [
            'title_ar' => __('dashboard.group_notification_title_ar'),
            'title_en' => __('dashboard.group_notification_title_en'),
            'message_ar' => __('dashboard.group_notification_message_ar'),
            'message_en' => __('dashboard.group_notification_message_en'),
        ];
    }
}
