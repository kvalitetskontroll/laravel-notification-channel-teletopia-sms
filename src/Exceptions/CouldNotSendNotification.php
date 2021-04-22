<?php

namespace Kvalitetskontroll\TeletopiaSMS\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function teletopiaSmsError(string $message, int $code)
    {
        return new static(sprintf('TeletopiaSms responded with error %d, message: %s', $code, $message), $code);
    }
}
