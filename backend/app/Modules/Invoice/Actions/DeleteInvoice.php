<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeleteInvoice
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice)
    {
        Gate::forUser($user)->authorize('delete', $invoice);

        // Make sure the invoice is still in `draft` status
        throw_validation_unless(
            $invoice->isDraft(),
            trans('invoices.message.failed.error-updating-not-draft'),
        );

        $invoice->delete();
    }
}
