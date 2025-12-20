<?php

namespace App\Notifications\Channel;

use Illuminate\Notifications\Notification;
use RuntimeException;

class BalanceChannel
{
    public function send($notifiable, Notification $notification)
    {
        return $notifiable->balances()->create(
            $this->buildPayload($notifiable, $notification)
        );
    }

    public function buildPayload($notifiable, Notification $notification)
    {
        return $this->getData($notifiable, $notification);
    }

    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toBalance')) {
            return is_array($data = $notification->toBalance($notifiable))
                ? $data : $data->data;
        }

        throw new RuntimeException('Notification is missing toBalance method.');
    }
}
