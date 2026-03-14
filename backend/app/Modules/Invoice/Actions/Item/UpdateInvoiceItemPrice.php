<?php

namespace App\Modules\Invoice\Actions\Item;

use App\Actions\Traits\AsAction;
use App\Events\Invoice\InvoiceItemUpdated;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateInvoiceItemPrice
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        InvoiceItem $item,
        array $inputs,
    ) {
        Gate::forUser($user)->authorize('manageItems', [$invoice, $item]);

        $validated = $this->validate($inputs);

        $item->fill($validated)
            ->save();

        event(new InvoiceItemUpdated($item));
    }

    public function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'price' => 'required|numeric|min:0',
        ])->validate();
    }
}
