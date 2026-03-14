<?php

namespace App\Modules\Invoice\Actions\Item;

use App\Actions\Traits\AsAction;
use App\Events\Invoice\InvoiceLayoutChanged;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceManualItem;
use App\Models\User;
use App\Modules\Invoice\Services\InvoiceManualItemService;
use Illuminate\Support\Facades\Gate;

class RemoveInvoiceItem
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        Invoice $invoice,
        InvoiceItem $invoiceItem,
    ) {
        Gate::forUser($user)->authorize('manageItems', [$invoice, $invoiceItem]);

        if (same_morph_alias(InvoiceManualItem::class, $invoiceItem->item_type)) {
            InvoiceManualItemService::make($user, $company)
                ->delete($invoiceItem->item_id);
        }

        $invoiceItem->delete();

        event(new InvoiceLayoutChanged($invoice));
    }
}
