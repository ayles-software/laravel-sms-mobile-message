<?php

namespace AylesSoftware\MobileMessage;

use RuntimeException;
use Illuminate\Support\Facades\Http;

class MobileMessageApi
{
    protected string $url = 'https://api.mobilemessage.com.au/v1/messages';

    public function sendSms(string $message, string $to, string $from)
    {
        if (strlen($message) > 5000) {
            throw new RuntimeException('Notification was not sent. Content length may not be greater than 670 characters.');
        }

        $response = Http::asJson()
            ->withBasicAuth(config('services.mobile_message.key'), config('services.mobile_message.secret'))
            ->post($this->url, [
                'messages' => [
                    [
                        'to' => $to,
                        'message' => $message,
                        'sender' => $from,
                        'unicode' => true,
                    ],
                ],
            ]);

        if (! $response->successful()) {
            return (object) [
                'success' => false,
                'message' => $message,
                'to' => $to,
                'from' => $from,
                'errorMessage' => $response->json('details.0') ?: $response->json('message'),
            ];
        }

        return (object) [
            'id' => $response->json('messages.0.message_id'),
            'message' => $message,
            'to' => $to,
            'from' => $from,
            'success' => true,
        ];
    }
}
