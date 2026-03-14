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

class AttachManualItem
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

        // Add items to the offer
        $offerService = OfferService::make($offer);

        $offerService->addItem(
            name: data_get($validated, 'name'),
            price: data_get($validated, 'selling_price'),
            description: data_get($validated, 'description'),
            details: $this->buildDetails($validated),
            quantity: data_get($validated, 'quantity'),
        );

        event(new OfferItemsUpdated($offer));

        return $offer;
    }

    protected function buildDetails(array $inputs): array
    {
        $unitId = data_get($inputs, 'unit');

        $unit = $this->catalogService->findUnitOrFail($unitId);
        $unitName = $this->catalogService->getUnitName($unit);

        return [
            'type' => data_get($inputs, 'type'),
            'unit_id' => $unit->id,
            'unit' => $unitName,
            'unit_name' => $unitName,
            'purchasing_price' => data_get($inputs, 'purchasing_price'),
            'selling_price' => data_get($inputs, 'selling_price'),
            'description' => data_get($inputs, 'description'),
            'name' => data_get($inputs, 'name'),
        ];
    }

    public function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'name' => 'required|string',
            'type' => 'required|string',
            'quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
            'unit' => [
                'required',
                'integer',
                'exists:catalog_item_units,id',
            ],
            'purchasing_price' => 'nullable',
            'selling_price' => 'required|numeric',
            'description' => 'required|string',
        ])->validate();
    }
}
