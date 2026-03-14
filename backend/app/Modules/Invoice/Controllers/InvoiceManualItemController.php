<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceManualItem;
use App\Modules\Invoice\Actions\ManualItem\AddInvoiceManualItem;
use App\Modules\Invoice\Actions\ManualItem\UpdateInvoiceManualItem;
use Illuminate\Http\Request;

class InvoiceManualItemController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        AddInvoiceManualItem::run(
            $request->user(),
            $request->company(),
            $invoice,
            $request->all(),
        );

        flash_success(__('invoices.manual-item.messages.manual-item-added'));

        return back();
    }

    public function update(Request $request, InvoiceManualItem $manualItem)
    {
        UpdateInvoiceManualItem::run(
            $request->user(),
            $request->company(),
            $manualItem,
            $request->all(),
        );

        flash_success(__('invoices.manual-item.messages.manual-item-updated'));

        return back();
    }
}
