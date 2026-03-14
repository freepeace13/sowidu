<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tax;
use App\Modules\Invoice\Actions\AddInvoiceTax;
use App\Modules\Invoice\Actions\RemoveInvoiceTax;
use Illuminate\Http\Request;

class InvoiceTaxController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        AddInvoiceTax::run($request->user(), $request->company(), $invoice, $request->all());

        flash_success(__('invoices.message.tax.added'));

        return back();
    }

    public function destroy(Request $request, Invoice $invoice, Tax $tax)
    {
        RemoveInvoiceTax::run($request->user(), $request->company(), $invoice, $tax);

        flash_success(__('invoices.message.tax.removed'));

        return back();
    }
}
