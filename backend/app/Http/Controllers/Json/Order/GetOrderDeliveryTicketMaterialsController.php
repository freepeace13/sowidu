<?php

namespace App\Http\Controllers\Json\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Transformers\DeliveryTicketMaterialTransformer;
use Illuminate\Http\Request;

class GetOrderDeliveryTicketMaterialsController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        $includedMaterials = $order->usedMaterials()
            ->pluck('delivery_ticket_material_id');

        return response()->json(
            $order->deliveryTicketsMaterials()
                ->with(['deliveryTicket'])
                ->whereNotIn('delivery_ticket_materials.id', $includedMaterials)
                ->search($request->get('q'))
                ->paginate(10)
                ->through(
                    fn ($deliveryTicketMaterial) => DeliveryTicketMaterialTransformer::make($deliveryTicketMaterial)->withDeliveryTicket()
                        ->resolve(),
                ),
        );
    }
}
