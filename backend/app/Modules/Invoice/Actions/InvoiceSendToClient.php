<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Enums\InvoiceStatus;
use App\Events\Invoice\InvoiceSent;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;

class InvoiceSendToClient
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice)
    {
        Gate::forUser($user)->authorize('sendToClient', $invoice);

        // Validate if invoice status is not `DRAFT`
        throw_validation_unless(
            $invoice->status == InvoiceStatus::DRAFT,
            trans('invoices.message.failed.error-sending'),
        );

        $invoiceService = InvoiceService::run($invoice);

        $invoiceService->sendToClient($invoice);
        $invoiceService->savePermanentInternalId();

        event(new InvoiceSent($invoice));
    }
}
