<?php

namespace App\Enums;

use App\Enums\MetaProperties\Color;
use App\Enums\MetaProperties\Trans;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Meta\Meta;
use ArchTech\Enums\Metadata;
use ArchTech\Enums\Values;

/**
 * @method color()
 * @method trans()
 */
#[Meta(Color::class, Trans::class)]
enum PaymentForm: int
{
    use InvokableCases;
    use Metadata;
    use Values;

    #[Color('#4CAF50'), Trans('work_log.payment_type.paid_via_payroll')]
    case PAID_VIA_PAYROLL = 1;

    #[Color('#2196F3'), Trans('work_log.payment_type.paid_via_incoming_invoice')]
    case PAID_VIA_INCOMING_INVOICE = 2;

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn ($case) => [
                'text' => $case->trans(),
                'value' => $case->value,
                'color' => $case->color(),
            ])
            ->toArray();
    }
}
