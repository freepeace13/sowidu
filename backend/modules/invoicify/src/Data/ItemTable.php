<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class ItemTable implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected array $items,
        protected array $totalsSummary,
        // protected string $totalPrice,
        // protected array $includedTaxes,
        // protected string $totalPriceWithTaxes,
        // protected array $appliedDeductions,
        // protected string $totalPriceAfterDeductions,
        protected ?string $title = '',
        protected ?string $caption = '',
    ) {}

    public function count(): int
    {
        return count($this->items);
    }
}
