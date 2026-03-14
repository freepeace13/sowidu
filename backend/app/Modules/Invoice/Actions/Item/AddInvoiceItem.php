<?php

namespace App\Modules\Invoice\Actions\Item;

use App\Actions\Traits\AsAction;
use App\Events\Invoice\InvoiceLayoutChanged;
use App\Models\CatalogItem;
use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\Invoice;
use App\Models\User;
use App\Modules\Invoice\InvoiceService;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddInvoiceItem
{
    use AsAction;

    public function handle(User $user, Company $company, Invoice $invoice, array $inputs)
    {
        Gate::forUser($user)->authorize('manageItems', $invoice);

        $validated = $this->validate($inputs);

        $invoiceService = InvoiceService::run($invoice);

        // Add item on the invoice
        if ($items = data_get($validated, 'catalog_items', [])) {
            $invoiceService->addProducts($user, $company, $items); // Save catalog item
        }

        if ($tickets = data_get($validated, 'delivery_tickets', [])) {
            $invoiceService->addTickets($user, $company, $tickets);
        }

        event(new InvoiceLayoutChanged($invoice));
    }

    public function validate(array $inputs)
    {
        // TODO validate if added tickets is already paid or already invoiced
        return Validator::make($inputs, [
            'delivery_tickets' => [
                Rule::requiredIf(!data_get($inputs, 'catalog_items', [])),
                'array',
            ],
            'delivery_tickets.*' => [
                'required',
                'integer',
                'exists:catalog_items,id',
                new OwnedByCompany(DeliveryTicket::class),
            ],

            'catalog_items' => [
                Rule::requiredIf(!data_get($inputs, 'delivery_tickets', [])),
                'array',
            ],
            'catalog_items.*' => [
                'required',
                'integer',
                'exists:catalog_items,id',
                new OwnedByCompany(CatalogItem::class),
            ],
        ])->validate();
    }
}
