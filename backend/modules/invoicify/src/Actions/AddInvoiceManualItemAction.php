<?php

namespace Modules\Invoicify\Actions;

use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Invoicify\Contracts\Actions\AddsInvoiceManualItems as AddsInvoiceManualItemsContract;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Models\InvoiceItem;
use Modules\Invoicify\Models\InvoiceManualItem;
use Modules\Invoicify\Services\InvoiceManualItemService;

class AddInvoiceManualItemAction implements AddsInvoiceManualItemsContract
{
    use AuthorizesRequests;

    public function add($user, Invoice $invoice, array $inputs, Company $company, $errorBag = null): InvoiceManualItem
    {
        Gate::forUser($user)->authorize('manageManualItems', $invoice);

        // Validate inputs
        $validated = $this->validate($inputs);

        $invoiceManualItem = InvoiceManualItemService::make($user, $company)->create($validated);

        $invoiceItem = $this->createInvoiceItem(
            $user,
            $invoiceManualItem->name,
            $invoiceManualItem->selling_price,
            $invoiceManualItem->quantity,
            $invoiceManualItem->description ?? '',
            $invoiceManualItem->toArray(),
        );

        $invoiceItem->item()->associate($invoiceManualItem);

        $invoice->items()->save($invoiceItem);

        return $invoiceManualItem;
    }

    protected function createInvoiceItem(
        $user,
        string $name,
        int|float $price,
        int|float $quantity = 1,
        ?string $description = null,
        array $details = [],
    ): InvoiceItem {
        $invoiceItem = InvoiceItem::make([
            'name' => trim($name),
            'quantity' => $quantity,
            'price' => $price,
            'description' => $description,
            'details' => $details,
        ]);

        $invoiceItem->user()->associate($user);

        return $invoiceItem;
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
