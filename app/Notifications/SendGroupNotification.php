<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Panel\GroupNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendGroupNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $notification;

    public function __construct(GroupNotification $notification)
    {
        $this->notification = $notification;
    }

    public function via($notifiable)
    {
        return ['fcm', 'custom_database'];
    }

    public function toFcm($notifiable)
    {
        return [
            'title' => $notifiable->lang == 'en' ? $this->notification->title_en : $this->notification->title_ar,
            'message' => $notifiable->lang == 'en' ? $this->notification->message_en : $this->notification->message_ar,
            'type' => NotificationType::GROUP_NOTIFICATION,
            'data' => [],
            'item_id' => null,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'title_ar' => $this->notification->title_ar,
            'message_ar' => $this->notification->message_ar,
            'title_en' => $this->notification->title_en,
            'message_en' => $this->notification->message_en,
            'type' => NotificationType::GROUP_NOTIFICATION,
            'user_type_id' => $notifiable->user_type_id,
            'item_id' => null,
        ];
    }
}
