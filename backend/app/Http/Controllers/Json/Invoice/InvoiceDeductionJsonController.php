<?php

namespace App\Http\Controllers\Json\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Repositories\InvoiceRepository;
use App\Transformers\InvoiceTransformer;
use Illuminate\Http\Request;

class InvoiceDeductionJsonController extends Controller
{
    public function forDeduction(Request $request, Order $order, Invoice $invoice)
    {
        return response()->json(
            InvoiceRepository::make(
                $request->user(),
                $request->company(),
            )
                ->filters(
                    $request->only([
                        'q',
                    ]),
                )
                ->deductionCandidates($invoice)
                ->with([
                    'biller',
                    'deliveryAddress',
                    'order',
                ])
                ->paginate($request->get(
                    'count',
                    15,
                ))
                ->through(
                    fn (Invoice $invoice) => (new InvoiceTransformer($invoice))
                        ->withBillerDetails()
                        ->withOrderDetails()
                        ->withStatus()
                        ->withOrderClientDetails($invoice->order)
                        ->withDeliveryAddress()
                        ->resolve(),
                ),
        );
    }
}
