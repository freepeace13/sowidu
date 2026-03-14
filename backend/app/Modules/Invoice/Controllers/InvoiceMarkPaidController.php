<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\InvoiceMarkAsPaid;
use Illuminate\Http\Request;

class InvoiceMarkPaidController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice)
    {
        InvoiceMarkAsPaid::run($request->user(), $request->company(), $invoice);

        flash_success(trans('invoices.message.mark-as-paid'));

        return back();
    }
}
