<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class Metadata implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $title,
        protected string $author,
        protected ?string $keywords = '',
        protected ?string $subject = '',
    ) {}
}
