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
use App\Modules\Invoice\InvoiceService;
use App\Rules\OwnedByCompany;
use App\Transformers\Addressbook\AddressbookTransformer;
use App\Transformers\CompanyTransformer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateInvoice
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        array $inputs,
    ) {
        Gate::forUser($user)->authorize('update', $invoice);

        $validated = $this->validate($inputs);

        if (
            Arr::exists($validated, 'notes')
            && !Arr::hasAny($validated, ['biller', 'order', 'delivery_address'])
        ) {
            return $invoice->update(Arr::only($validated, ['notes']));
        }

        if (
            Arr::exists($validated, 'payment_date')
            && !Arr::hasAny($validated, ['biller', 'order', 'delivery_address'])
        ) {
            return $invoice->update(Arr::only($validated, ['payment_date']));
        }

        if (
            Arr::hasAny($validated, [
                'execution_period_start',
                'execution_period_end',
                'construction_site',
            ])
        ) {

            return $invoice->update([
                ...Arr::only(
                    $validated,
                    [
                        'execution_period_start',
                        'execution_period_end',
                    ],
                ),
                'construction_site_id' => data_get($validated, 'construction_site.id'),
            ]);
        }

        $invoice->fill(
            array_merge(Arr::only(
                $validated,
                ['delivery_date', 'external_id', 'type', 'payment_date', 'notes', 'kind'],
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

        $invoice->save();

        $invoice->update([
            'biller_details' => $this->getBillerTransformed($invoice),
        ]);

        InvoiceService::run($invoice)->resetPreviewLayout();
    }

    protected function getBillerTransformed(Invoice $invoice)
    {
        $biller = $invoice->loadMissing('biller')
            ->biller;
        if (same_class(Company::class, $biller)) {
            return CompanyTransformer::make($biller)
                ->withColumnValues()
                ->resolve();
        }

        return AddressbookTransformer::make(
            $invoice->loadMissing('biller')
                ->biller,
        )->withAddress()
            ->resolve();
    }

    public function validate(array $inputs)
    {
        if (
            Arr::exists($inputs, 'notes')
            && !Arr::hasAny($inputs, ['biller', 'order', 'delivery_address'])
        ) {
            return Validator::make($inputs, [
                'notes' => 'nullable|string',
            ])->validate();
        }

        if (
            Arr::exists($inputs, 'payment_date')
            && !Arr::hasAny($inputs, ['biller', 'order', 'delivery_address'])
        ) {
            return Validator::make($inputs, [
                'payment_date' => 'required|date|date_format:Y-m-d',
            ])->validate();
        }

        if (
            Arr::hasAny($inputs, ['execution_period_start', 'execution_period_end', 'construction_site'])
        ) {
            return Validator::make($inputs, [
                'execution_period_start' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                ],
                'execution_period_end' => [
                    'nullable',
                    'date',
                    'date_format:Y-m-d',
                ],
                'construction_site' => 'nullable|array',
                'construction_site.id' => [
                    'nullable',
                    'integer',
                    'exists:places,id',
                ],
            ])->validate();
        }

        // Default rules
        $rules = [
            'external_id' => 'nullable',
            'type' => [
                'required',
                Rule::in(InvoiceType::values()),
            ],
            'notes' => 'nullable',
            'payment_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            'biller.type' => [
                'required',
                'string',
                Rule::in([
                    (new Addressbook)->getMorphClass(),
                    (new Company)->getMorphClass(),
                ]),
            ],
            'biller.id' => [
                'required',
                'integer',
            ],
            'order' => 'required|array',
            'order.id' => [
                'required',
                'integer',
                new OwnedByCompany(Order::class, 'team_id'),
            ],
            'delivery_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
            'delivery_address' => 'required|array',
            'delivery_address.id' => [
                'required',
                'integer',
                'exists:places,id',
            ],
            'kind' => [
                'required',
                Rule::in(InvoiceKind::values()),
            ],
        ];

        if (data_get($inputs, 'type') === InvoiceType::INCOMING()) {
            $incomingRules = [
                'delivery_date' => [
                    'required',
                    'date',
                    'date_format:Y-m-d',
                ],
                'payment_date' => [
                    'required',
                    'date',
                    'date_format:Y-m-d',
                ],
            ];

            $rules = array_merge($rules, $incomingRules);
        }
        // // Outgoing invoice
        // $outgoingRules = [];

        // $rules = array_merge($rules, $outgoingRules);

        return Validator::make($inputs, $rules)->validate();
    }
}
