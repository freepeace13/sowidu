<?php

namespace Modules\Invoicify\Contracts\Actions;

use Modules\Invoicify\Models\Invoice;

interface GeneratesInvoicePdf
{
    public function generate($user, Invoice $invoice, $teamId = null, $errorBag = null): mixed;
}
