<?php

namespace Modules\Invoicify\Contracts\Actions;

use Modules\Invoicify\Models\Invoice;

interface MarksInvoicesAsPaid
{
    public function markAsPaid($user, Invoice $invoice, $teamId = null, $errorBag = null): Invoice;
}
