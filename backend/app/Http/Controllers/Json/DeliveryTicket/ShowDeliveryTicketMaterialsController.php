<?php

namespace App\Http\Controllers\Json\DeliveryTicket;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use App\Transformers\DeliveryTicketMaterialTransformer;
use Illuminate\Http\Request;

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
