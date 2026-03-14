<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateInvoiceSubjectAndDescription
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        array $inputs,
    ): Invoice {

        Gate::forUser($user)->authorize('update', $invoice);

        $validated = $this->validate($inputs);

        $invoice->update($validated);

        InvoiceService::run($invoice)->resetPreviewLayout();

        return $invoice;
    }

    protected function validate(array $inputs)
    {
        return Validator::make(
            $inputs,
            [
                'subject' => ['nullable', 'string', 'max:60'],
                'description' => ['nullable', 'string'],
                'notes' => ['nullable', 'string'],
            ],
        )->validate();
    }
}
