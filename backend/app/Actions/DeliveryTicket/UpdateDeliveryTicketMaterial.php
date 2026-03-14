<?php

namespace App\Actions\DeliveryTicket;

use App\Actions\Traits\AsAction;
use App\Events\DeliveryTicket\DeliveryTicketMaterialsUpdated;
use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateDeliveryTicketMaterial
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        DeliveryTicket $deliveryTicket,
        DeliveryTicketMaterial $material,
        array $inputs,
    ) {
        Gate::forUser($user)->authorize('manageMaterials', $deliveryTicket);

        $validated = $this->validate($inputs);

        $material->update(data_only(
            $validated,
            ['quantity', 'purchasing_price', 'selling_price'],
        ));

        $deliveryTicket->update([
            'total_purchasing_price' => $deliveryTicket
                ->materials()
                ->selectRaw('SUM(quantity * purchasing_price) as total_purchasing_price')
                ->value('total_purchasing_price'),
            'total_selling_price' => $deliveryTicket
                ->materials()
                ->selectRaw('SUM(quantity * selling_price) as total_selling_price')
                ->value('total_selling_price'),
        ]);

        $this->syncDetails($material);

        event(new DeliveryTicketMaterialsUpdated($material));
    }

    protected function syncDetails(DeliveryTicketMaterial $material)
    {
        $details = $material->details;
        $details->put('purchasing_price', $material->purchasing_price);
        $details->put('selling_price', $material->selling_price);

        $material->update(['details' => $details]);
    }

    protected function validate(array $inputs)
    {
        return Validator::make($inputs, [
            'quantity' => [
                'sometimes',
                'numeric',
                'min:0',
                'required_without_all:purchasing_price,selling_price',
            ],
            'purchasing_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
        ])->validate();
    }
}
