<?php

namespace Modules\DeliveryTicket\Http\Controllers\Json;

use App\Http\Controllers\Controller;
use App\Transformers\DeliveryTicketTransformer;
use Illuminate\Http\Request;
use Modules\DeliveryTicket\Contracts\Services\DeliveryTicketsServiceContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class GetDeliveryTicketController extends Controller
{
    public function __construct(
        protected DeliveryTicketsServiceContract $deliveryTicketsService,
    ) {
        $this->deliveryTicketsService = $deliveryTicketsService;
    }

    public function __invoke(Request $request)
    {
        return response()->json(
            $this->deliveryTicketsService->make($request->user(), $request->company())
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
