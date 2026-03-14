<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Tax;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;

class RemoveInvoiceTax
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        Tax $tax,
    ): Invoice {
        Gate::forUser($user)->authorize('manageTaxes', $invoice);

        $invoice->taxes()
            ->detach($tax);

        InvoiceService::run($invoice)->resetPreviewLayout();

        return $invoice->load('taxes');
    }
}
