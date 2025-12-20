<?php

namespace App\Notifications\Channel;

use Illuminate\Notifications\Notification;
use RuntimeException;

class CustomDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Database\Eloquent\Model | array
     */
    public function send($notifiable, Notification $notification)
    {
        if ($notifiable->receive_notification == 0) {
            return [];
        }

        return $notifiable->notifications()->create(
            $this->buildPayload($notifiable, $notification)
        );
    }

    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        return $this->getData($notifiable, $notification) +
            ['notification_type' => method_exists($notification, 'databaseType') ? $notifiable->databaseType($notification) : get_class($notification)];
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     *
     * @throws RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toDatabase')) {
            return is_array($data = $notification->toDatabase($notifiable))
                ? $data : $data->data;
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException('Notification is missing toDatabase / toArray method.');
    }
}
