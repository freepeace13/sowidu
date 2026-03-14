<?php

namespace App\Http\Controllers\Json\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use App\Transformers\InvoiceTransformer;
use Illuminate\Http\Request;

/**
 * TODO - Not used!
 */
class GetInvoicesController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(
            // InvoiceRepository::make(
            //     $request->user(),
            //     $request->company(),
            // )->filters(
            //     $request->only([
            //         'q',
            //         'dateAdded',
            //         'invoiceStatus',
            //         'type',
            //         'paymentDate',
            //         'plannedStartDate',
            //         'plannedFinishDate',
            //     ]),
            // )
            //     ->with([
            //     'client',
            //     'biller',
            //     'taxes',
            //     'deliveryAddress',
            //     'order',
            //     'order.currentPlace',
            // ])
            //     ->latest()
            //     ->paginate($request->get('count', 15))
            //     ->through(
            //         fn (Invoice $invoice) => (new InvoiceTransformer($invoice))->withBillerDetails()
            //             ->withOrderDetails()
            //             ->withAmounts()
            //             ->withTaxes()
            //             ->withStatus()
            //             ->withOrderClientDetails($invoice->order)
            //             ->withDeliveryAddress()
            //             ->resolve(),
            //     ),
        );
    }
}
