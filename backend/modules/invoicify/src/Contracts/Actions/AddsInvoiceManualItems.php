<?php

namespace Modules\Invoicify\Contracts\Actions;

use App\Models\Company;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Models\InvoiceManualItem;

interface AddsInvoiceManualItems
{
    public function add($user, Invoice $invoice, array $inputs, Company $company, $errorBag = null): InvoiceManualItem;
}
