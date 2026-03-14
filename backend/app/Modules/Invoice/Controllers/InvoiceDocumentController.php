<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\AddDocumentToInvoice;
use App\Modules\Invoice\Actions\RemoveDocumentOnInvoice;
use Illuminate\Http\Request;

class InvoiceDocumentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        AddDocumentToInvoice::run($request->user(), $invoice, $request->all());

        flash_success(trans('invoices.message.documents.added'));

        return back();
    }

    public function destroy(Request $request, Invoice $invoice, Attachment $document)
    {
        RemoveDocumentOnInvoice::run(
            $request->user(),
            $invoice,
            $document,
        );

        flash_success(trans('invoices.message.documents.removed'));

        return back();
    }
}
