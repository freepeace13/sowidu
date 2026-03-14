<?php

namespace App\Enums;

use App\Enums\MetaProperties\Color;
use App\Enums\MetaProperties\Trans;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

// #[Color('#BDBDBD'), Icon('edit_note'), Trans('invoices.payments.methods.cash')]
/**
 * @method color()
 * @method trans()
 */
#[Meta(Color::class, Trans::class)]
enum PaymentMethod: int
{
    use InvokableCases;
    use Metadata;
    use Options;
    use Values;

    #[Color('#A5D6A7'), Trans('invoices.payments.methods.cash')]
    case CASH = 0;

    #[Color('#B3E5FC'), Trans('invoices.payments.methods.credit_card')]
    case CREDIT_CARD = 1;

    #[Color('#F8BBD0'), Trans('invoices.payments.methods.bank_transfer')]
    case BANK_TRANSFER = 2;

    #[Color('#9575CD'), Trans('invoices.payments.methods.check')]
    case CHECK = 3;

    #[Color('#FFECB3'), Trans('invoices.payments.methods.others')]
    case OTHERS = 4;
}
