<?php

namespace App\Enums;

class ContactRequestStatus extends Enum
{
    const Pending = 'pending';
    const Accepted = 'accepted';
    const Denied = 'denied';
    const Blocked = 'blocked';
}
