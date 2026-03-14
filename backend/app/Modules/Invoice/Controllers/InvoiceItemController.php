<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Modules\Invoice\Actions\Item\AddInvoiceItem;
use App\Modules\Invoice\Actions\Item\RemoveInvoiceItem;
use App\Modules\Invoice\Actions\Item\UpdateInvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        AddInvoiceItem::run(
            $request->user(),
            $request->company(),
            $invoice,
            $request->all(),
        );

        flash_success(trans('invoices.message.items.added'));

        return back();
    }

    public function update(Request $request, Invoice $invoice, InvoiceItem $item)
    {
        UpdateInvoiceItem::run(
            $request->user(),
            $request->company(),
            $invoice,
            $item,
            $request->all(),
        );

        flash_success(trans('invoices.message.items.quantity-updated'));

        return back();
    }

    public function destroy(Request $request, Invoice $invoice, InvoiceItem $item)
    {
        RemoveInvoiceItem::run(
            $request->user(),
            $request->company(),
            $invoice,
            $item,
        );

        flash_success(trans('invoices.message.items.removed'));

        return back();
    }
}
