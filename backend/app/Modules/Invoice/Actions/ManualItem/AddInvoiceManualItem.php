<?php

namespace App\Modules\Invoice\Actions\ManualItem;

use App\Actions\Traits\AsAction;
use App\Events\Invoice\InvoiceLayoutChanged;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceManualItem;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use App\Modules\Invoice\Services\InvoiceManualItemService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AddInvoiceManualItem
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        array $inputs,
    ): InvoiceManualItem {
        Gate::forUser($user)->authorize('manageManualItems', $invoice);

        // Validate inputs
        $validated = $this->validate($inputs);

        $invoiceManualItem = InvoiceManualItemService::make($user, $company)
            ->create($validated);

        // Add the manual item to the invoice
        InvoiceService::run($invoice)
            ->addManualItem($user, $company, $invoiceManualItem);

        event(new InvoiceLayoutChanged($invoice));

        return $invoiceManualItem;
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
