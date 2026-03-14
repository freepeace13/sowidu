<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class CareOf implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected ?string $careOfName = null,
        protected ?string $careOfLegalForm = null,
        protected ?string $careOfAddress = null,
    ) {}
}
