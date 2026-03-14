<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Enums\InvoiceStatus;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;

class InvoiceMarkAsPaid
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice)
    {
        Gate::forUser($user)->authorize('markAsPaid', $invoice);

        // Validate if invoice status is still `DRAFT`
        throw_validation_unless(
            $invoice->status == InvoiceStatus::SENT,
            trans('invoices.message.failed.error-sending'),
        );

        InvoiceService::run($invoice)->markAsPaid();
    }
}
