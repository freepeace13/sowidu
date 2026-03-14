<?php

namespace App\Traits;

use App\Values\Notifiable\EmailRecipient;
use App\Values\Notifiable\MobileRecipient;

trait CreatesNotifiableRecipient
{
    protected function createRecipient(string $recipient)
    {
        if (is_email($recipient)) {
            return EmailRecipient::make($recipient);
        }

        return MobileRecipient::make($recipient);
    }
}
