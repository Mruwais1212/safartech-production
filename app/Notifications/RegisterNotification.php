<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Enums\UserType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegisterNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['fcm', 'custom_database'];
    }

    public function toFcm($notifiable)
    {
        return [
            'title' => __('messages.registered_title', [], $notifiable->lang),
            'message' => __('messages.registered_message', [], $notifiable->lang),
            'type' => NotificationType::REGISTER,
            'data' => [],
            'item_id' => null,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'title_ar' => __('messages.registered_title', [], 'ar'),
            'message_ar' => __('messages.registered_message', [], 'ar'),
            'title_en' => __('messages.registered_title', [], 'en'),
            'message_en' => __('messages.registered_message', [], 'en'),
            'type' => NotificationType::REGISTER,
            'user_type_id' => UserType::CLIENT,
            'item_id' => null,
        ];
    }
}
