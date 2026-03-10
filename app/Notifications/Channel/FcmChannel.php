<?php

namespace App\Notifications\Channel;

use const DATE_ATOM;

use DateTimeImmutable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\Messaging\NotFound as MessagingNotFound;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use RuntimeException;
use Throwable;

class FcmChannel
{
    public function send($notifiable, Notification $notification)
    {
        $tokens = $notifiable->deviceTokens()->pluck('device_token')->toArray();

        if (empty($tokens) || $notifiable->receive_notification == 0) {
            return [];
        }

        $data = $this->getData($notifiable, $notification);

        return $this->sendToFcm($data, $tokens);
    }

    protected function sendToFcm($data, $tokens)
    {
        try {
            $factory = (new Factory)->withServiceAccount(base_path(config('services.firebase.credentials')));
            $auth = $factory->createAuth();
            $cloudMessaging = $factory->createMessaging();
            $notification = FcmNotification::create($data['title'], $data['message']);

            $data = [
                'data' => json_encode([
                    'notification_type' => $data['type'],
                    'notification_title' => $data['title'],
                    'notification_message' => $data['message'],
                    'notification_data' => $data['data'],
                    'item_id' => $data['item_id'],
                ]),
            ];

            // $message = CloudMessage::new();
            // $message->withNotification($notification)
            //     ->withData([
            //         'data' => json_encode([
            //             'notification_type' => $data['type'],
            //             'notification_title' => $data['title'],
            //             'notification_message' => $data['message'],
            //             'notification_data' => $data['data'],
            //             'item_id' => $data['item_id'],
            //         ]),
            //     ]);

            if (count($tokens) > 0) {
                foreach ($tokens as $key => $token) {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification($notification)
                        ->withData($data);
                    $cloudMessaging->send($message);
                }
            }
        } catch (MessagingNotFound $e) {
            Log::error('The target device could not be found.'.$e->getMessage());
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
            Log::error('The given message is malformatted.'.$e->getMessage());
        } catch (\Kreait\Firebase\Exception\Messaging\ServerUnavailable $e) {
            $retryAfter = $e->retryAfter();
            Log::error('The FCM servers are currently unavailable. Retrying at '.$retryAfter->format(DATE_ATOM));
            while ($retryAfter <= new DateTimeImmutable) {
                sleep(1);
            }
            $cloudMessaging->send($message);
        } catch (\Kreait\Firebase\Exception\Messaging\ServerError $e) {
            Log::error('The FCM servers are down.');
        } catch (MessagingException $e) {
            Log::error('Unable to send message: '.$e->getMessage());
        } catch (Throwable $th) {
            Log::error($th->getMessage().'  from '.$th->getFile().' on line '.$th->getLine());
        }

        return true;
    }

    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toFcm')) {
            return is_array($data = $notification->toFcm($notifiable))
                ? $data : $data->data;
        }

        throw new RuntimeException('Notification is missing toFcm method.');
    }
}
