<?php

namespace App\Enums;

use App\Enums\MetaProperties\Trans;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

/**
 * @method trans()
 */
#[Meta(Trans::class)]
enum InvoiceKind: int
{
    use InvokableCases;
    use Metadata;
    use Options;
    use Values;

    #[Trans('invoices.labels.partial-invoice-1')]
    case PARTIAL_1 = 1;

    #[Trans('invoices.labels.partial-invoice-2')]
    case PARTIAL_2 = 2;

    #[Trans('invoices.labels.partial-invoice-3')]
    case PARTIAL_3 = 4;

    #[Trans('invoices.labels.partial-invoice-4')]
    case PARTIAL_4 = 5;

    #[Trans('invoices.labels.partial-invoice-5')]
    case PARTIAL_5 = 6;

    #[Trans('invoices.labels.final-invoice')]
    case FINAL = 3;
}
