<?php

namespace App\Actions\DeliveryTicket;

use App\Actions\Traits\AsAction;
use App\Events\DeliveryTicket\DeliveryTicketMaterialsAdded;
use App\Models\CatalogItem;
use App\Models\Company;
use App\Models\DeliveryTicket;
use App\Models\User;
use App\Rules\OwnedByCompany;
use App\Services\DeliveryTicketMaterialService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AddMaterialToDeliveryTicket
{
    use AsAction;

    public function handle(
        User $user,
        Company $company,
        DeliveryTicket $deliveryTicket,
        array $inputs,
    ) {
        Gate::forUser($user)->authorize('manageMaterials', $deliveryTicket);

        $validated = $this->validate($inputs);

        $materials = DeliveryTicketMaterialService::make($deliveryTicket)
            ->forUser($user)
            ->forCompany($company)
            ->addMaterials(data_get($validated, 'products', []));

        event(new DeliveryTicketMaterialsAdded($materials, $user));
    }

    protected function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'products' => [
                'required',
                'array',
            ],
            'products.*' => [
                'required',
                'integer',
                'exists:catalog_items,id',
                new OwnedByCompany(CatalogItem::class),
            ],
        ])->validate();
    }
}
