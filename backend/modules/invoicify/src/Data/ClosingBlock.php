<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class ClosingBlock implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $paymentDate,
        protected string $totalWageCosts,
        protected ?string $closingRemarks = '',
    ) {}
}
