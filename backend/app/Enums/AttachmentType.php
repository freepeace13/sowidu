<?php

namespace App\Enums;

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum AttachmentType: int
{
    use From, InvokableCases, Names, Options, Values;

    case MEDIA = 1;
    case DOCUMENT = 2;
    case INCOMING_INVOICE = 3;
    case OUTGOING_INVOICE = 4;
}
