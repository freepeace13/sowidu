<?php

namespace App\Observers;

use App\Models\DeliveryTicket;

class DeliveryTicketObserver
{
    /**
     * Handle the DeliveryTicket "created" event.
     *
     * @return void
     */
    public function created(DeliveryTicket $deliveryTicket)
    {
        $deliveryTicket->update([
            'total_purchasing_price' => $deliveryTicket->materials()->sum(column: 'purchasing_price'),
            'total_selling_price' => $deliveryTicket->materials()->sum(column: 'selling_price'),
        ]);
    }

    /**
     * Handle the DeliveryTicket "updated" event.
     *
     * @return void
     */
    public function updating(DeliveryTicket $deliveryTicket)
    {
        dd($deliveryTicket);
        $deliveryTicket->update([
            'total_purchasing_price' => $deliveryTicket->materials()->sum(column: 'purchasing_price'),
            'total_selling_price' => $deliveryTicket->materials()->sum(column: 'selling_price'),
        ]);
    }

    /**
     * Handle the DeliveryTicket "updated" event.
     *
     * @return void
     */
    public function updated(DeliveryTicket $deliveryTicket)
    {
        dd($deliveryTicket);
        $deliveryTicket->update([
            'total_purchasing_price' => $deliveryTicket->materials()->sum(column: 'purchasing_price'),
            'total_selling_price' => $deliveryTicket->materials()->sum(column: 'selling_price'),
        ]);
    }

    /**
     * Handle the DeliveryTicket "deleted" event.
     *
     * @return void
     */
    public function deleted(DeliveryTicket $deliveryTicket)
    {
        //
    }

    /**
     * Handle the DeliveryTicket "restored" event.
     *
     * @return void
     */
    public function restored(DeliveryTicket $deliveryTicket)
    {
        //
    }

    /**
     * Handle the DeliveryTicket "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(DeliveryTicket $deliveryTicket)
    {
        //
    }
}
