<?php

namespace Kvalitetskontroll\TeletopiaSMS;

class TeletopiaSmsMessage
{
    /**
     * The content of the message.
     *
     * @var string
     */
    public string $message;

    /**
     * An optional prefix on the message
     *
     * @var string|null
     */
    public ?string $greeting = null;

    /**
     * An optional suffix on the message
     *
     * @var string|null
     */
    public ?string $salutation = null;

    /**
     * The sender information for the message.
     *
     * @var string
     */
    public string $sender;

    /**
     * The recipients phone number of the message.
     * The phone number should be a number without leading '+',
     * only the country code and the subscriber`s number including the area code.
     *
     * @var array
     */
    public array $recipients = [];

    /**
     * Set the greeting of the message.
     *
     * @param string $greeting
     * @return $this
     */
    public function greeting(string $greeting): TeletopiaSmsMessage
    {
        $this->greeting = $greeting;

        return $this;
    }

    /**
     * Set the content of the message.
     *
     * @param string $message
     * @return $this
     */
    public function message(string $message): TeletopiaSmsMessage
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the salutation of the message.
     *
     * @param string $salutation
     * @return $this
     */
    public function salutation(string $salutation): TeletopiaSmsMessage
    {
        $this->salutation = $salutation;

        return $this;
    }

    public function content(): string
    {
        $returnValue = "";

        if ($this->greeting) {
            $returnValue .= $this->greeting . "\n";
        }

        $returnValue .= $this->message;

        if ($this->salutation) {
            $returnValue .= "\n" . $this->salutation;
        }

        return $returnValue;
    }

    /**
     * Set the sms sender.
     *
     * @param string $sender
     * @return $this
     */
    public function sender(string $sender): TeletopiaSmsMessage
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Set the recipients phone number of the message.
     * The phone number should be a number without leading '+',
     * only the country code and the subscriber`s number including the area code.
     *
     * @param array|string $recipients
     * @return $this
     */
    public function recipients($recipients): TeletopiaSmsMessage
    {
        if (! is_array($recipients)) {
            $recipients = [$recipients];
        }

        $this->recipients = $recipients;

        return $this;
    }
}
