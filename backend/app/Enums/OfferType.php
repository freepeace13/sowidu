<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum OfferType: string
{
    use InvokableCases;
    use Options;
    use Values;

    case INCOMING = 'incoming';

    case OUTGOING = 'outgoing';
}
