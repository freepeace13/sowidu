<?php

namespace App\Actions\DeliveryTicket\Import;

use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Arr;

class CreateOrIgnoreImport
{
    public $ticket;

    public function create(array $ticket): CreateOrIgnoreImport
    {

        $this->ticket = DeliveryTicket::updateOrCreate([
            'external_id' => $ticket['external_id'],
        ], Arr::only($ticket, ['delivery_date', 'type', 'company_id']));

        return $this;
    }

    public function applyCompany(Company $company): CreateOrIgnoreImport
    {
        if ($company) {
            $this->ticket->company()->associate($company);
        }

        return $this;
    }

    public function applyUser(User $user): CreateOrIgnoreImport
    {
        $this->ticket->user()->associate($user);

        return $this;
    }

    public function applyDeliveryAddress(?int $deliveryAddressId): CreateOrIgnoreImport
    {
        if ($deliveryAddressId) {
            $this->ticket->deliveryAddress()->associate($deliveryAddressId);
        }

        return $this;
    }

    public function applyOrder(Order $order): CreateOrIgnoreImport
    {
        // dd($order);
        $this->ticket->order()->associate($order);

        return $this;
    }

    public function applyDeliverer(int $delivererId): CreateOrIgnoreImport
    {
        $this->ticket->deliverer()->associate($delivererId);

        return $this;
    }

    public function applyMaterials(array $items): CreateOrIgnoreImport
    {
        foreach ($items as $item) {
            $cleanName = strtoupper(preg_replace('/\s+/', '', trim($item['unitRaw'])));

            $unit = CatalogItemUnit::firstOrCreate([
                'name' => $cleanName,
            ]);

            $catalog = array_merge($item, [
                'selling_price' => 0,
                'unit' => $unit->id,
                'unit_name' => $unit->name,
                'user_id' => $this->ticket->user->id ?? null,
                'company_id' => $this->ticket->company->id ?? null,
            ]);

            $material = CatalogItem::updateOrCreate([
                'name' => $item['name'],
            ], Arr::only($catalog, [
                'internal_id',
                'vendor_id',
                'unit',
                'unit_name',
                'manufacture_id',
                'purchasing_price',
                'selling_price',
                'description',
            ]));

            $this->ticket->materials()->updateOrCreate([
                'details' => $material->toArray(),
            ], [
                'user_id' => $this->ticket->user->id,
                'quantity' => $item['quantity'] ?? 0,
                'purchasing_price' => $material->purchasing_price,
                'selling_price' => $material->selling_price,
            ]);
        }

        return $this;
    }

    public function finally(): DeliveryTicket
    {
        $this->ticket->save();

        return $this->ticket;
    }
}
