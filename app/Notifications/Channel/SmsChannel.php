<?php

namespace App\Notifications\Channel;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
// use MFrouh\Sms4jawaly\Facades\Sms4jawaly;
use RuntimeException;
use Throwable;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        try {
            // $data = $this->getData($notifiable, $notification);
            // Sms4jawaly::sendSms($data['message'], array_key_exists('phone', $data) ? $data['phone'] : $notifiable->phone, array_key_exists('phone_code', $data) ? $data['phone_code'] : $notifiable->phone_code);
        } catch (Throwable $th) {
            Log::error($th->getMessage());
        }

        return $notifiable;
    }

    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toSms')) {
            return is_array($data = $notification->toSms($notifiable))
                ? $data : $data->data;
        }

        if (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException('Notification is missing toSms / toArray method.');
    }
}
