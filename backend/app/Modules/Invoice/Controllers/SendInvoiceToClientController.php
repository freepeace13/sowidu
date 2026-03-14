<?php

namespace App\Modules\Invoice\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Modules\Invoice\Actions\InvoiceSendToClient;
use Illuminate\Http\Request;

class SendInvoiceToClientController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice)
    {
        InvoiceSendToClient::run(
            $request->user(),
            $request->company(),
            $invoice,
        );

        flash_success(trans('invoices.message.success.sending-to-client'));

        return back();
    }
}
