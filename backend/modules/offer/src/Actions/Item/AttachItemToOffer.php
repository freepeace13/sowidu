<?php

namespace Modules\Offer\Actions\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Offer\Actions\Traits\AsAction;
use Modules\Offer\Contracts\External\CatalogServiceContract;
use Modules\Offer\Events\OfferItemsUpdated;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class AttachItemToOffer
{
    use AsAction;

    public function __construct(
        protected CatalogServiceContract $catalogService,
    ) {}

    public function handle(Model $user, Model $company, Offer $offer, array $inputs): Offer
    {
        // Authorization check
        Gate::forUser($user)->authorize('manageItems', $offer);

        // Validate inputs
        $validated = $this->validate($inputs);

        if ($items = data_get($validated, 'items', [])) {
            // Add items to the offer
            $offerService = OfferService::make($offer);

            foreach ($items as $itemId) {
                $item = $this->catalogService->findItemOrFail($itemId);
                $item->load('media');

                $offerService->addItem(
                    name: $item->name,
                    price: $item->selling_price,
                    description: $item->description ?? '',
                    details: $this->buildItemDetails($item),
                    quantity: 1,
                );
            }
        }

        event(new OfferItemsUpdated($offer));

        return $offer;
    }

    protected function buildItemDetails(Model $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'type' => $item->type ?? null,
            'selling_price' => $item->selling_price,
            'media' => $item->media?->toArray() ?? [],
        ];
    }

    public function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'items' => [
                'required',
                'array',
            ],
            'items.*' => [
                'required',
                'integer',
                'exists:catalog_items,id',
            ],
        ])->validate();
    }
}
