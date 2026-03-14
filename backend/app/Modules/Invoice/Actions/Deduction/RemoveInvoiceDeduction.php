<?php

namespace App\Modules\Invoice\Actions\Deduction;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceDeduction;
use App\Models\User;

class RemoveInvoiceDeduction
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice, InvoiceDeduction $invoiceDeduction): void
    {
        // TODO - check user if authorize
        $invoiceDeduction->delete();
    }
}
