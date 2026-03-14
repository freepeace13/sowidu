<?php

namespace Modules\DeliveryTicket\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\DeliveryTicket\Contracts\Actions\AddMaterialToDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\RemoveMaterialOnDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryTicketMaterialContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class DeliveryTicketMaterialController extends Controller
{
    public function __construct(
        protected AddMaterialToDeliveryTicketContract $addMaterialToDeliveryTicketAction,
        protected RemoveMaterialOnDeliveryTicketContract $removeMaterialOnDeliveryTicketAction,
        protected UpdateDeliveryTicketMaterialContract $updateDeliveryTicketMaterialAction,
    ) {
        $this->addMaterialToDeliveryTicketAction = $addMaterialToDeliveryTicketAction;
        $this->removeMaterialOnDeliveryTicketAction = $removeMaterialOnDeliveryTicketAction;
        $this->updateDeliveryTicketMaterialAction = $updateDeliveryTicketMaterialAction;
    }

    public function store(Request $request, DeliveryTicket $deliveryTicket)
    {
        $this->addMaterialToDeliveryTicketAction->handle($request->user(), $request->company(), $deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.materials.added'));

        return back();
    }

    public function update(
        Request $request,
        DeliveryTicket $deliveryTicket,
        DeliveryTicketMaterial $material,
    ) {
        $this->updateDeliveryTicketMaterialAction->handle(
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
        $this->removeMaterialOnDeliveryTicketAction->handle(
            $request->user(),
            $request->company(),
            $deliveryTicket,
            $material,
        );

        flash_success(trans('delivery_tickets.message.materials.remove'));

        return back();
    }
}
