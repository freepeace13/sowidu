<?php

namespace App\Enums;

use App\Enums\MetaProperties\Description;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;

#[Meta(Description::class)]
enum OrderType: int
{
    use InvokableCases;
    use Metadata;

    #[Description('Incoming')]
    case INCOMING = 0;

    #[Description('Outgoing')]
    case OUTGOING = 1;
}
