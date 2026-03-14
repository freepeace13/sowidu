<?php

namespace Modules\DeliveryTicket\Services;

use App\Models\CatalogItem;
use App\Transformers\Catalog\CatalogItemTransformer;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class DeliveryTicketMaterialService
{
    protected $user;

    protected $company;

    public function __construct(protected DeliveryTicket $deliveryTicket) {}

    public static function make(DeliveryTicket $deliveryTicket): self
    {
        return new static($deliveryTicket);
    }

    public function forUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function forCompany($company): self
    {
        $this->company = $company;

        return $this;
    }

    public function addMaterials(array $catalogItems)
    {
        $materialModels = collect($catalogItems)
            ->map(function ($catalogItem) {
                $item = CatalogItem::with(['type', 'media'])->findOrFail($catalogItem);
                $details = $this->transformCatalogItemToMaterial($item);

                $material = DeliveryTicketMaterial::make([
                    'quantity' => 1,
                    'details' => $details,
                    'purchasing_price' => data_get($details, 'purchasing_price'),
                    'selling_price' => data_get($details, 'selling_price'),
                ]);

                $material->deliveryTicket()
                    ->associate($this->deliveryTicket);

                return $material;
            });

        $this->deliveryTicket->materials()
            ->saveMany($materialModels);

        $this->deliveryTicket->update([
            'total_purchasing_price' => $this->deliveryTicket->materials()->sum('purchasing_price'),
            'total_selling_price' => $this->deliveryTicket->materials()->sum('selling_price'),
        ]);

        return $materialModels;
    }

    protected function transformCatalogItemToMaterial(CatalogItem $item): array
    {
        return (new CatalogItemTransformer($item))
            ->withMedia($item->media)
            ->withType($item->type)
            ->resolve();
    }
}
