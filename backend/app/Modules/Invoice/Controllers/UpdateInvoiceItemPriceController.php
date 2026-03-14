<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Modules\Invoice\Actions\Item\UpdateInvoiceItemPrice;
use Illuminate\Http\Request;

class UpdateInvoiceItemPriceController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice, InvoiceItem $item)
    {
        UpdateInvoiceItemPrice::run(
            $request->user(),
            $request->company(),
            $invoice,
            $item,
            $request->all(),
        );

        flash_success(__('invoices.items.notifications.item_price_updated'));

        return back();
    }
}
