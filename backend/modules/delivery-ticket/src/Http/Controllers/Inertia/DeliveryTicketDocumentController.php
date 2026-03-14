<?php

namespace Modules\DeliveryTicket\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DeliveryTicket\Actions\RemoveDocumentToDeliveryTicket;
use Modules\DeliveryTicket\Contracts\Actions\AddDocumentToDeliveryTicketContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketDocument;

class DeliveryTicketDocumentController extends Controller
{
    public function __construct(
        protected AddDocumentToDeliveryTicketContract $addDocumentToDeliveryTicketAction,
        protected RemoveDocumentToDeliveryTicket $removeDocumentToDeliveryTicketAction,
    ) {
        $this->addDocumentToDeliveryTicketAction = $addDocumentToDeliveryTicketAction;
        $this->removeDocumentToDeliveryTicketAction = $removeDocumentToDeliveryTicketAction;
    }

    public function store(Request $request, DeliveryTicket $deliveryTicket)
    {
        $this->addDocumentToDeliveryTicketAction->handle($request->user(), $deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.documents.added'));

        return back();
    }

    public function destroy(Request $request, DeliveryTicket $deliveryTicket, DeliveryTicketDocument $document)
    {
        $this->removeDocumentToDeliveryTicketAction->handle($request->user(), $deliveryTicket, $document);

        flash_success(trans('delivery_tickets.message.documents.removed'));

        return back();
    }
}
