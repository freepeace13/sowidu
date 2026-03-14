<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;

enum OrderEvent: string
{
    use InvokableCases;

    case CREATED = 'created';
    case CANCELLED = 'cancelled';

    case CONFIRMED = 'confirmed';

    case START_WORKING = 'start_working';
    case FINISH_WORKING = 'finish_working';

    case CLIENT_REJECTED = 'client_rejected';
    case CLIENT_ACCEPTED = 'client_accepted';

    case COMPLETED = 'completed';
}
