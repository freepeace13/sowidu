<?php

namespace Modules\Invoicify\Contracts\Actions;

use Modules\Invoicify\Models\Invoice;

interface SendsInvoiceToClient
{
    public function send($user, Invoice $invoice, $teamId = null, $errorBag = null);
}
