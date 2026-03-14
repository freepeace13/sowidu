<?php

namespace App\Notifications\Common;

class MemberAdded extends DynamicResourceNotification
{
    /**
     * The notification message pattern.
     *
     * @return string
     */
    protected function message()
    {
        return ':subject added you to :resource.';
    }
}
