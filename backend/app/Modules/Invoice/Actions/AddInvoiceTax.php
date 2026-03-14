<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Tax;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AddInvoiceTax
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        array $inputs,
    ): Invoice {
        Gate::forUser($user)->authorize('manageTaxes', $invoice);

        // Make sure the invoice is still in `draft` status
        throw_validation_unless(
            $invoice->isDraft(),
            trans('invoices.message.failed.error-updating-not-draft'),
        );

        $validated = $this->validate($inputs);

        $invoice->taxes()
            ->attach($validated['tax']);

        InvoiceService::run($invoice)->resetPreviewLayout();

        return $invoice->load('taxes');
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'tax' => [
                'required',
                'exists:taxes,id',
                new OwnedByCompany(Tax::class),
            ],
        ])->validate();
    }
}
