<?php

namespace App\Notifications\Sms;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResetPasswordActivationCodeSms extends Notification implements ShouldQueue
{
    use Queueable;

    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['sms'];
    }

    public function toSms($notifiable)
    {
        return [
            'message' => __('messages.your_activation_code_to_reset_password_in_your_account_is', ['code' => $this->code], $notifiable->lang),
        ];
    }
}
