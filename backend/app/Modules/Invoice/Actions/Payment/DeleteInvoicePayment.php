<?php

namespace App\Modules\Invoice\Actions\Payment;

use App\Actions\Traits\AsAction;
use App\Events\Invoice\InvoicePaymentVoided;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;

class DeleteInvoicePayment
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice, InvoicePayment $invoicePayment)
    {
        // Validate and delete
        Gate::forUser($user)->authorize('managePayments', $invoice);

        $invoicePayment->delete();

        InvoiceService::run($invoice)->updateInvoiceStatus();

        // Update invoice items to reflect the voided payment
        event(new InvoicePaymentVoided($invoice));

    }
}
