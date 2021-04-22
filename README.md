## TeletopiaSMS Notifications Channel for Laravel

This package makes it easy to send notifications using [TeletopiaSMS](https://www.teletopiasms.no/p/) with Laravel 5.5+, 6.x and 7.x

## Contents

- [Installation](#installation)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Installation

- Install via composer

- Add the configuration to your `services.php` config file:

```
'teletopiasms' => [
    'user' => env('SERVICES_TELETOPIASMS_USER'),
    'password' => env('SERVICES_TELETOPIASMS_PASSWORD'),
    'url' => '', // api url
    'sender' => '', // sender name
    'whitelist' => array_filter(array_map('trim', explode(',', env('SERVICES_TELETOPIASMS_WHITELIST', '')))),
],
```

- Add credentials to the `.env` file

```
SERVICES_TELETOPIASMS_USER='' // username
SERVICES_TELETOPIASMS_PASSWORD='' // password
SERVICES_TELETOPIASMS_WHITELIST='' // comma separated list of phones which could get sms on local/beta
```

## Usage

You can use the channel in your `via()` method inside the notification:

```
use Illuminate\Notifications\Notification;
use Kvalitetskontroll\TeletopiaSMS\TeletopiaSmsMessage;
use Kvalitetskontroll\TeletopiaSMS\TeletopiaSmsChannel;

class WelcomeNotification extends Notification
{
    public function via($notifiable)
    {
        return [TeletopiaSmsChannel::class];
    }

    public function toTeletopiasms($notifiable)
    {
        return (new TeletopiaSmsMessage)
            ->message("Welcome to our system");
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForTeletopiasms()` method, which returns a phone number with the country code. Else you need to specify the phone number manually.

```
public function routeNotificationForTeletopiasms()
{
    return $this->phone; // 4712345678
}
```

### Available Message methods

#### Required

string `message()`: Sets the content of the notification message.

#### Optional

array|string `recipients()`: Sets the recipient number(s) of the sms.

_* The phone number should be a number without leading '+', only the country code and the subscriber`s number including the area code._

string `sender()`: Sets the sender's name or phone number.

string `salutation()`: Sets the salutation of the message.

string `greeting()`: Sets the greeting of the message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Security

If you discover any security related issues, please email it@kvalitetskontroll.no instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
