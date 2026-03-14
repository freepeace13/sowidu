<?php

namespace App\Modules\Invoice\Actions\ManualItem;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\InvoiceManualItem;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateInvoiceManualItem
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        InvoiceManualItem $manualItem,
        array $inputs,
    ): InvoiceManualItem {
        Gate::forUser($user)->authorize('manageManualItems', $manualItem->invoice);

        // Validate inputs
        $validated = $this->validate($inputs);

        return tap($manualItem)->update(Arr::only($validated, [
            'name',
            'internal_id',
            'vendor_id',
            'quantity',
            'purchasing_price',
            'selling_price',
            'description',
        ]));
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'name' => 'required|string',
            'type' => 'required|string',
            'vendor_id' => 'nullable',
            'quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
            'unit' => [
                'required',
                'integer',
                'exists:catalog_item_units,id',
            ],
            'purchasing_price' => 'nullable',
            'selling_price' => 'required|numeric',
            'description' => 'required|string',
        ])->validate();
    }
}
