# MobileMessage sms notifications channel for Laravel 12

This package makes it easy to send notifications using [MobileMessage](https://mobilemessage.com.au/) with Laravel 12.

## Installation

Install the package via composer:
```bash
composer require ayles-software/laravel-sms-mobile-message
```

Add your MobileMessage api key, secret and optional default sender sms_from to your `config/services.php`:

```php
'mobile_message' => [
    'key' => env('MOBILE_MESSAGE_KEY'),
    'secret'  => env('MOBILE_MESSAGE_SECRET'),
    'from' => env('MOBILE_MESSAGE_FROM'),
],
```

## Usage

Use MobileMessageChannel in `via()` method inside your notification classes. Example:

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use AylesSoftware\MobileMessage\MobileMessageChannel;
use AylesSoftware\MobileMessage\MobileMessageMessage;

class SmsTest extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via($notifiable)
    {
        return [MobileMessageChannel::class];
    }

    public function toMobileMessage($notifiable)
    {
        return (new MobileMessageMessage)
            ->message("SMS test to user #{$notifiable->id} with token {$this->token} by MobileMessage")
            ->from('Dory');
    }
}
```

In notifiable model (User), include method `routeNotificationForMobileMessage()` that returns recipient mobile number:

```php
public function routeNotificationForMobileMessage()
{
    return $this->phone;
}
```

Then send a notification the standard way:
```php
$user = User::find(1);

$user->notify(new SmsTest);
```

## Events
Following events are triggered by Notification. By default:
- Illuminate\Notifications\Events\NotificationSending
- Illuminate\Notifications\Events\NotificationSent

NotificationFailed will trigger if something goes wrong
- Illuminate\Notifications\Events\NotificationFailed

## Testing

Nope

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
