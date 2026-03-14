<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

enum OfferActionType: string
{
    use InvokableCases;

    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}
