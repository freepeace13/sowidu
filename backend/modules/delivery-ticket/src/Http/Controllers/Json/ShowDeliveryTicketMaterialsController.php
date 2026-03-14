<?php

namespace Modules\DeliveryTicket\Http\Controllers\Json;

use App\Http\Controllers\Controller;
use App\Transformers\DeliveryTicketMaterialTransformer;
use Illuminate\Http\Request;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class ShowDeliveryTicketMaterialsController extends Controller
{
    public function __invoke(Request $request, DeliveryTicket $deliveryTicket)
    {
        abort_if(
            !$deliveryTicket->isOwnedByCompany($request->company()),
            403,
            trans('validation.403'),
        );

        return response()->json(
            $deliveryTicket
                ->materials()
                ->get()
                ->map(
                    fn (DeliveryTicketMaterial $deliveryTicketMaterial) => (new DeliveryTicketMaterialTransformer($deliveryTicketMaterial))
                        ->resolve(),
                ),
        );
    }
}
