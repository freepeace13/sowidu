<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use InvalidArgumentException;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class Sender implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $name,
        // A path is required for rendering images NOT urls
        protected string $logoImgPath,
        protected string $presentAddress,
        protected ?string $emailAddress = '',
        protected ?string $websiteUrl = '',
        protected string $legalForm = '',
    ) {
        if (!file_exists($this->logoImgPath)) {
            throw new InvalidArgumentException('Logo image not found.');
        }
    }
}
