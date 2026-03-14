<?php

namespace App\Http\Controllers\Inertia\DeliveryTicket;

use App\Actions\DeliveryTicket\AddDocumentToDeliveryTicket;
use App\Actions\DeliveryTicket\RemoveDocumentToDeliveryTicket;
use App\Http\Controllers\Controller;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketDocument;
use Illuminate\Http\Request;

class DeliveryTicketDocumentController extends Controller
{
    public function store(Request $request, DeliveryTicket $deliveryTicket)
    {
        AddDocumentToDeliveryTicket::run($request->user(), $deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.documents.added'));

        return back();
    }

    public function destroy(Request $request, DeliveryTicket $deliveryTicket, DeliveryTicketDocument $document)
    {
        RemoveDocumentToDeliveryTicket::run($request->user(), $deliveryTicket, $document);

        flash_success(trans('delivery_tickets.message.documents.removed'));

        return back();
    }
}
