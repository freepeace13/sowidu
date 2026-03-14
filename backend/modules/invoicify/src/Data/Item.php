<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class Item implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $sequenceNo,
        protected string $quantity,
        protected string $unit,
        protected string $description,
        protected string $unitPrice,
        protected string $totalPrice,
    ) {}
}
