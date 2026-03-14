<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Enums\InvoiceKind;
use App\Enums\InvoiceType;
use App\Models\Attachment;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class CreateInvoice
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs): Invoice
    {
        Gate::forUser($user)->authorize('create', Invoice::class);

        $validated = $this->validate($inputs);
        $invoice = null;

        if ($validated['type'] === InvoiceType::INCOMING()) {
            $invoice = \App\Modules\Invoice\Actions\CreateIncomingInvoice::run($user, $company, $inputs);
        }

        if ($validated['type'] === InvoiceType::OUTGOING()) {
            $invoice = \App\Modules\Invoice\Actions\CreateOutgoingInvoice::run($user, $company, $inputs);
        }

        throw_if(blank($invoice), new \Exception(trans('alerts.error.500')));

        // Save documents
        $documents = collect(data_get($validated, 'documents'))
            ->map(fn ($mediaUuid) => (new Attachment([
                'media_file_id' => Media::findByUuid($mediaUuid)->getKey(),
                'user_id' => $user->getKey(),
            ])));

        $invoice->documents()
            ->saveMany($documents);

        $invoiceService = InvoiceService::run($invoice);

        $invoiceService->autoGenerateItems($user);

        $invoiceService->attachTaxes();

        $invoiceService->attachInvoiceDeductions(
            data_get($validated, 'invoice_deductions', []),
        );

        $invoiceService->saveInvoiceDefaults($company);

        return $invoice;
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'type' => [
                'required',
                Rule::in(InvoiceType::values()),
            ],
            'documents' => [
                'nullable',
                'array',
            ],
            'documents.*' => [
                'required',
                'exists:media_files,uuid',
            ],
            'kind' => [
                'required',
                Rule::in(InvoiceKind::values()),
            ],
            'care_of_id' => [
                'nullable',
            ],
            'invoice_deductions' => [
                'nullable',
                'array',
            ],
            'invoice_deductions.*' => [
                'required',
                'exists:invoices,id',
                new OwnedByCompany(Invoice::class),
            ],
        ])->validate();
    }
}
