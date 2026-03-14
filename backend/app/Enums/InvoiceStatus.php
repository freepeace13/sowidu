<?php

namespace App\Enums;

use App\Enums\MetaProperties\Color;
use App\Enums\MetaProperties\Icon;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;

/**
 * @method color()
 * @method icon()
 */
#[Meta(Color::class, Icon::class)]
enum InvoiceStatus: int
{
    use InvokableCases;
    use Metadata;

    #[Color('#BDBDBD'), Icon('edit_note')]
    case DRAFT = 0;

    #[Color('#2196F3'), Icon('pending_actions')]
    case SENT = 1; // Sent or Unpaid

    #[Color('#4caf50'), Icon('task_alt')]
    case PAID = 2;

    // New Statuses
    #[Color('#AB47BC'), Icon('call_split')]
    case PARTIALLY_PAID = 3;

    #[Color('#F44336'), Icon('warning')]
    case OVERPAID = 4;
}
