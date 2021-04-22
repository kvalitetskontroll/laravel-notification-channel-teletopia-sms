<?php

namespace Kvalitetskontroll\TeletopiaSMS;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kvalitetskontroll\TeletopiaSMS\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Exception;

class TeletopiaSmsChannel
{
    /**
     * Send sms message through teletopia.
     *
     * @param $notifiable
     * @param Notification $notification
     * @throws Exception
     */
    public function send($notifiable, Notification $notification)
    {
        // get the message from the notification
        $message = $notification->toTeletopiaSms($notifiable);
        // get the sms content
        $content = $message->content();
        // if sender - get it; else get the global sender
        $sender = $message->sender ?? config('services.teletopiasms.sender');

        $recipients = [];

        // if the notifiable model has route for this notification - get it
        if ($modelRouteNotification = $notifiable->routeNotificationFor('TeletopiaSms')) {
            $recipients = [$modelRouteNotification];
        }

        // if there are specified recipients in the notification - get them
        if (! empty($message->recipients)) {
            // get the message recipients
            $recipients = $message->recipients;
        }

        // remove duplicates
        $recipients = array_unique($recipients);

        $validRecipients = [];
        $invalidRecipients = [];

        // if there are no recipients - throw an error
        if (empty($recipients)) {
            throw new Exception('SMS: Missing phone numbers to send message.');
        }

        // go through each recipient and check if it's a valid number
        foreach ($recipients as $recipient) {
            // if the value is numeric and in the whitelist - add it to the valid recipients
            // else - make it invalid
            if (is_numeric($recipient) && $this->inSMSWhitelist($recipient)) {
                // make sure that the phone number is string
                $validRecipients[] = (string) $recipient;
            } else {
                $invalidRecipients[] = (string) $recipient;
            }
        }

        if (empty($validRecipients)) {
            $invalidRecipientsString = implode(', ', $invalidRecipients);

            $message = "SMS: The following phone numbers are invalid: $invalidRecipientsString";

            if (App::environment('local') || App::environment('testing')) {
                $message = "SMS: The following phone numbers are invalid" .
                    " or not in the sms whitelist: $invalidRecipientsString";
            }

            Log::error($message);

            return;
        }

        // create the post data
        $data = [
            "auth" => [
                'username' => config('services.teletopiasms.user'),
                'password' => config('services.teletopiasms.password'),
            ],
            "messages" => [],
        ];

        // add a message for each recipient
        foreach ($validRecipients as $recipient) {
            $data['messages'][] = [
                "sender" => $sender,
                "recipient" => $recipient,
                "contentText" => [
                    "text" => $content
                ]
            ];
        }

        // make request to send this message(s)
        $request = Http::asJson()->post(config('services.teletopiasms.url'), $data);

        if ($request->failed()) {
            throw CouldNotSendNotification::teletopiaSmsError($request->body() ?? '', $request->status() ?? 500);
        }
    }

    private function inSMSWhitelist($recipient): bool
    {
        // if the environment isn't local AND not testing - return true
        if (! App::environment('local') && ! App::environment('testing')) {
            return true;
        }

        // get the whitelist
        $whitelist = config('services.teletopiasms.whitelist');

        // check if the recipient is in the env sms whitelist
        return in_array($recipient, $whitelist);
    }
}
