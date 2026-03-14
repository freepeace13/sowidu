<?php

namespace Modules\DeliveryTicket\Http\Controllers\Json;

use App\Http\Controllers\Controller;
use App\Transformers\DeliveryTicketTransformer;
use Illuminate\Http\Request;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class ShowDeliveryTicketController extends Controller
{
    public function __invoke(Request $request, DeliveryTicket $deliveryTicket)
    {
        abort_if(
            !$deliveryTicket->isOwnedByCompany($request->company()),
            403,
            trans('validation.403'),
        );

        $transformer = (new DeliveryTicketTransformer($deliveryTicket->loadMissing([
            'order',
            'documents.media',
        ])))
            ->withDelivererDetails()
            ->withOrderFullDetails($deliveryTicket->order)
            ->withDeliveryAddress();

        if ($request->get('full', false)) {
            $transformer->withDelivererFullDetails()->withDocuments();
        }

        return response()->json($transformer->resolve());
    }
}
