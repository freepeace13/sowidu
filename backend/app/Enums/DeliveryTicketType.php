<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum DeliveryTicketType: int
{
    use InvokableCases;
    use Options;
    use Values;

    case INCOMING = 1;

    case OUTGOING = 2;
}
