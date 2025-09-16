<?php

namespace AylesSoftware\MobileMessage;

use Exception;
use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Events\NotificationFailed;

class MobileMessageChannel
{
    public function __construct(public MobileMessageApi $client, public Dispatcher $events)
    {
    }

    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toMobileMessage')) {
            throw new Exception('Please implement toMobileMessage() to send an SMS');
        }

        $message = $notification->toMobileMessage($notifiable);

        $result = $this->client->sendSms(
            $message->message,
            $message->to ?: $notifiable->routeNotificationForMobileMessage(),
            $message->from ?: config('services.mobile_message.from'),
        );

        if (! $result->success) {
            $this->events->dispatch(
                new NotificationFailed($notifiable, $notification, get_class($this), (array) $result)
            );

            throw new Exception('Notification failed '.$result->errorMessage);
        }

        return $result;
    }
}
