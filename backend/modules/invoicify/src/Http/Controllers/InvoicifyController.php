<?php

namespace Modules\Invoicify\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Invoicify\Actions\Preview\UpdateInvoice;
use Modules\Invoicify\Models\Invoice;
use Modules\Shared\Http\Controllers\InertiaController;

class InvoicifyController extends InertiaController
{
    public function update(Request $request, Invoice $invoice)
    {
        UpdateInvoice::run(
            $request->user(),
            $request->company(),
            $invoice,
            $request->all(),
        );

        flash_success(trans('invoices.message.success.updating'));

        return back();
    }
}
