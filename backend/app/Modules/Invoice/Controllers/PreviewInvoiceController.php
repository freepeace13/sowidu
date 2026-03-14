<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PreviewInvoiceController extends Controller
{
    public function __invoke(Request $request, $invoice)
    {
        $invoice = Invoice::findByIdOrUuid($invoice)->firstOrFail();

        if (!$request->user()) {
            return redirect()->route('invoice.pdf.stream', $invoice);
        }

        return redirect()->route('invoices.show', $invoice->id);
    }
}
