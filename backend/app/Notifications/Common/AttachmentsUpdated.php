<?php

namespace App\Notifications\Common;

class AttachmentsUpdated extends DynamicResourceNotification
{
    /**
     * The notification message pattern.
     *
     * @return string
     */
    protected function message()
    {
        return ':subject made some changes to :resource attachment(s).';
    }
}
