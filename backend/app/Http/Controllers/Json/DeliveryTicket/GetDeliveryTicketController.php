<?php

namespace App\Http\Controllers\Json\DeliveryTicket;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTicket;
use App\Services\DeliveryTicketsService;
use App\Transformers\DeliveryTicketTransformer;
use Illuminate\Http\Request;

class GetDeliveryTicketController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json(
            DeliveryTicketsService::make($request->user(), $request->company())
                ->filters($request->only(['q', 'deliveryDates', 'type', 'invoiceStatus']))
                ->with([
                    'deliverer',
                    'deliveryAddress',
                    'order',
                    'invoices',
                    'materials',
                ])
                ->latest()
                ->paginate($request->get('count', 15))
                ->through(
                    function (DeliveryTicket $ticket) {
                        return (new DeliveryTicketTransformer($ticket))
                            ->withIsPaidStatus()
                            ->withDelivererDetails()
                            ->withOrderDetails()
                            ->withDeliveryAddress()
                            ->withInvoicesStatus($ticket->invoices)
                            ->withTotalPurchasingPrice($ticket->materials)
                            ->withTotalSellingPrice($ticket->materials)
                            ->resolve();
                    },

                ),

        );
    }
}
