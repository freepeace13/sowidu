<?php

namespace Modules\Invoicify\Data;

use ArrayAccess;
use Modules\Invoicify\Data\Concerns\ArrayAccessible;

class InvoiceDetails implements ArrayAccess
{
    use ArrayAccessible;

    public function __construct(
        protected string $invoiceNo,
        protected string $invoiceDate,
        protected string $orderNo,
        protected string $constructionSite,
        protected string $executionPeriod,
        protected string $serviceRecipient,
        protected ?string $legalForm = null,
    ) {}
}
