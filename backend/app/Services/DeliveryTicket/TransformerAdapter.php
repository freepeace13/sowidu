<?php

namespace App\Services\DeliveryTicket;

use App\Transformers\Catalog\CatalogItemTransformer;
use App\Transformers\DeliveryTicketMaterialTransformer;
use App\Transformers\DeliveryTicketTransformer;
use App\Transformers\MediaTransformer;
use Modules\DeliveryTicket\Contracts\External\TransformerContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

class TransformerAdapter implements TransformerContract
{
    public function transformDeliveryTicket(DeliveryTicket $ticket, array $options = []): array
    {
        return (new DeliveryTicketTransformer($ticket))->resolve();
    }

    public function transformMaterial(DeliveryTicketMaterial $material, array $options = []): array
    {
        return (new DeliveryTicketMaterialTransformer($material))->resolve();
    }

    public function transformCatalogItem(mixed $catalogItem, array $options = []): array
    {
        $transformer = new CatalogItemTransformer($catalogItem);

        if (!empty($options['withMedia']) && $catalogItem->media) {
            $transformer->withMedia($catalogItem->media);
        }

        if (!empty($options['withType']) && $catalogItem->type) {
            $transformer->withType($catalogItem->type);
        }

        return $transformer->resolve();
    }

    public function transformMedia(mixed $media): array
    {
        return (new MediaTransformer($media))->resolve();
    }
}
