<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Modules\Invoice\Actions\Payment\AddInvoicePayment;
use App\Modules\Invoice\Actions\Payment\DeleteInvoicePayment;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        AddInvoicePayment::run(
            $request->user(),
            $request->company(),
            $invoice,
            $request->all(),
        );

        flash_success(trans('invoices.payments.messages.payment_added'));

        return back();
    }

    public function update(Request $request, Invoice $invoice, InvoicePayment $invoicePayment) {}

    public function destroy(
        Request $request,
        Invoice $invoice,
        InvoicePayment $invoicePayment,
    ) {
        DeleteInvoicePayment::run(
            $request->user(),
            $request->company(),
            $invoice,
            $invoicePayment,
        );

        flash_success(trans('invoices.payments.messages.payment_deleted'));

        return back();
    }
}
