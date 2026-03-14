<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Contracts\External;

use Modules\DeliveryTicket\Models\DeliveryTicket;
use Modules\DeliveryTicket\Models\DeliveryTicketMaterial;

/**
 * Contract for external transformation operations.
 *
 * Provides transformation of external models and delivery ticket data.
 */
interface TransformerContract
{
    /**
     * Transform a delivery ticket for API response.
     */
    public function transformDeliveryTicket(DeliveryTicket $ticket, array $options = []): array;

    /**
     * Transform a delivery ticket material for API response.
     */
    public function transformMaterial(DeliveryTicketMaterial $material, array $options = []): array;

    /**
     * Transform a catalog item for API response.
     *
     * @param  mixed  $catalogItem  The catalog item to transform
     * @param  array  $options  Options like 'withMedia', 'withType' to include related data
     */
    public function transformCatalogItem(mixed $catalogItem, array $options = []): array;

    /**
     * Transform media/document for API response.
     */
    public function transformMedia(mixed $media): array;
}
