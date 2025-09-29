<?php

namespace AylesSoftware\MobileMessage;

use RuntimeException;
use Illuminate\Support\Facades\Http;

class MobileMessageApi
{
    protected string $url = 'https://api.mobilemessage.com.au/v1/messages';

    public function sendSms(string $message, string $to, string $from)
    {
        if (strlen($message) > 1530) {
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
                'errorMessage' => $response->json('results.0.error.0'),
            ];
        }

        if ($response->json('results.0.status') === 'error') {
            return (object) [
                'success' => false,
                'message' => $message,
                'to' => $to,
                'from' => $from,
                'errorMessage' => $response->json('results.0.error.0'),
            ];
        }

        return (object) [
            'id' => $response->json('results.0.message_id'),
            'message' => $message,
            'to' => $to,
            'from' => $from,
            'success' => true,
        ];
    }
}
