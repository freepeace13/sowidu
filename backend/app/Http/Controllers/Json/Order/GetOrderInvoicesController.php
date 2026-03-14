<?php

namespace App\Http\Controllers\Json\Order;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Transformers\InvoiceTransformer;
use Illuminate\Http\Request;

class GetOrderInvoicesController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        return OrderRepository::make($request->user(), $request->company())
            ->onOrder($order)
            ->fetchDeductionInvoiceCandidates()
            ->map(function (Invoice $invoice) {
                return InvoiceTransformer::make($invoice)
                    ->withTotalPrice($invoice->grand_total, $invoice->currency())
                    ->withStatus()
                    ->resolve();
            });
    }
}
