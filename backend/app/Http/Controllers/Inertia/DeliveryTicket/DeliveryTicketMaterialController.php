<?php

namespace App\Http\Controllers\Inertia\DeliveryTicket;

use App\Actions\DeliveryTicket\AddMaterialToDeliveryTicket;
use App\Actions\DeliveryTicket\RemoveMaterialOnDeliveryTicket;
use App\Actions\DeliveryTicket\UpdateDeliveryTicketMaterial;
use App\Http\Controllers\Controller;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use Illuminate\Http\Request;

class DeliveryTicketMaterialController extends Controller
{
    public function store(Request $request, DeliveryTicket $deliveryTicket)
    {
        AddMaterialToDeliveryTicket::run($request->user(), $request->company(), $deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.materials.added'));

        return back();
    }

    public function update(
        Request $request,
        DeliveryTicket $deliveryTicket,
        DeliveryTicketMaterial $material,
    ) {
        UpdateDeliveryTicketMaterial::run(
            $request->user(),
            $request->company(),
            $deliveryTicket,
            $material,
            $request->all(),
        );

        flash_success(trans('delivery_tickets.message.materials.update'));

        return back();
    }

    public function destroy(
        Request $request,
        DeliveryTicket $deliveryTicket,
        DeliveryTicketMaterial $material,
    ) {
        RemoveMaterialOnDeliveryTicket::run(
            $request->user(),
            $request->company(),
            $deliveryTicket,
            $material,
        );

        flash_success(trans('delivery_tickets.message.materials.remove'));

        return back();
    }
}
