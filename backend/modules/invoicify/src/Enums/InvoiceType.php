<?php

namespace Modules\Invoicify\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum InvoiceType: int
{
    use InvokableCases;
    use Options;
    use Values;

    case INCOMING = 1;

    case OUTGOING = 2;
}
