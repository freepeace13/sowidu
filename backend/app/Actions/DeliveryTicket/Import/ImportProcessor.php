<?php

namespace App\Actions\DeliveryTicket\Import;

use Closure;
use Illuminate\Http\Request;

class ImportProcessor
{
    public function handle(Request $request, Closure $next)
    {
        $order = $request->route('order');

        foreach ($request->input('deliveryTickets') as $deliveryTicket) {
            $dateString = $deliveryTicket['deliveryDate'] ?? '';

            $dateTime = \DateTime::createFromFormat('d.m.Y', $dateString);
            $date = $dateTime ? (string) $dateTime->format('Y-m-d') : now()->format('Y-m-d');
            $place_id = $order->contractorAddressbook->current_place_id ?? null;

            $deliveryTicket[] = (new CreateOrIgnoreImport)
                ->create([
                    'delivery_date' => $date,
                    'external_id' => $deliveryTicket['externalId'],
                    'type' => 2,
                    'company_id' => $request->company()->id,
                ])
                ->applyCompany($request->company())
                ->applyUser($request->user())
                ->applyOrder($order)
                ->applyDeliveryAddress($place_id)
                ->applyMaterials($deliveryTicket['items'])
                ->finally()
                ->id;
        }

        return $next($request);
    }
}
