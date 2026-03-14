<?php

namespace App\Modules\Invoice\Actions\Payment;

use App\Actions\Traits\AsAction;
use App\Enums\PaymentMethod;
use App\Events\Invoice\InvoiceWasPaid;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AddInvoicePayment
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice, array $inputs)
    {
        Gate::forUser($user)->authorize('managePayments', $invoice);

        $validated = $this->validate($inputs);

        $invoicePayment = InvoicePayment::create($validated);
        $invoicePayment->invoice()->associate($invoice);
        $invoicePayment->save();

        InvoiceService::run($invoice)->updateInvoiceStatus();

        event(new InvoiceWasPaid($invoice));

        return $invoicePayment;
    }

    protected function validate(array $inputs)
    {
        return validator($inputs, [
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric'],
            'reference_number' => ['nullable', 'string'],
            'method' => ['required', Rule::in(PaymentMethod::values())],
            'payer_name' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ])->validated();
    }
}
