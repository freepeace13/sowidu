<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Actions;

use App\Models\CatalogItem;
use App\Rules\OwnedByCompany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\DeliveryTicket\Contracts\Actions\AddMaterialToDeliveryTicketContract;
use Modules\DeliveryTicket\Events\DeliveryTicketMaterialsAdded;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Services\DeliveryTicketMaterialService;

class AddMaterialToDeliveryTicket implements AddMaterialToDeliveryTicketContract
{
    public function handle(
        $user,
        $company,
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
