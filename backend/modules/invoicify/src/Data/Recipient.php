<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class Recipient implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $name,
        protected string $deliveryAddress,
        protected string $returnAddress,
        protected string $additionalNote,
        protected ?string $legalForm = null,
    ) {}
}
