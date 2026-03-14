<?php

namespace App\Modules\Invoice\Actions;

use App\Actions\Traits\AsAction;
use App\Enums\InvoiceKind;
use App\Enums\InvoiceType;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use App\Rules\OwnedByCompany;
use App\Services\CompanyService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateOutgoingInvoice
{
    use AsAction;

    public function handle(User $user, Company $company, array $inputs)
    {
        Gate::forUser($user)->authorize('create', Invoice::class);

        $validated = $this->validate($inputs);

        $paymentTerms = CompanyService::make($company)
            ->getPaymentTerms();
        if ($paymentTerms) {
            data_fill($validated, 'payment_date', today()->addDays($paymentTerms));
        }

        $invoice = Invoice::make(
            array_merge(Arr::only(
                $validated,
                ['delivery_date', 'external_id', 'type', 'payment_date', 'kind', 'care_of_id'],
            ), [
                'biller_id' => data_get($validated, 'biller.id'),
                'biller_type' => data_get($validated, 'biller.type'),
            ]),
        );

        $invoice->company()
            ->associate($company);
        $invoice->user()
            ->associate($user);

        $invoice->deliveryAddress()
            ->associate(data_get($validated, 'delivery_address.id'));

        $invoice->order()
            ->associate(data_get($validated, 'order.id'));

        return tap($invoice)->save();
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'biller' => 'required|array',
            'biller.id' => [
                'required',
                'integer',
            ],
            'biller.type' => [
                'required',
                'string',
                Rule::in([
                    (new Addressbook)->getMorphClass(),
                    (new Company)->getMorphClass(),
                ]),
            ],
            'order' => 'required|array',
            'order.id' => [
                'required',
                'integer',
                new OwnedByCompany(Order::class, 'team_id'),
            ],
            'delivery_address' => 'required|array',
            'delivery_address.id' => [
                'required',
                'integer',
                'exists:places,id',
            ],
            'delivery_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            'payment_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            'external_id' => [
                'nullable',
            ],
            'type' => [
                'required',
                Rule::in(InvoiceType::values()),
            ],
            'kind' => [
                'required',
                Rule::in(InvoiceKind::values()),
            ],
            'care_of_id' => [
                'nullable',
            ],
        ])->validate();
    }
}
