<?php

namespace Modules\Invoicify\Actions;

use App\Actions\Catalog\CatalogItemValidation;
use App\Actions\Catalog\CreateCatalogItemType;
use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use App\Models\Company as Team;
use App\Models\User;
use App\Services\MediaFileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Modules\Invoicify\Contracts\Actions\UpdatesInvoiceManualItems as UpdatesInvoiceManualItemsContract;

class UpdateInvoiceManualItemAction implements UpdatesInvoiceManualItemsContract
{
    use AuthorizesRequests;
    use CatalogItemValidation;

    public function update($user, CatalogItem $catalogItem, array $inputs, $teamId = null, $errorBag = null): CatalogItem
    {
        // Validate if user has authorization to make this action
        $this->authorizeForUser($user, 'update', [$catalogItem, $teamId]);

        if (is_int($teamId)) {
            $teamId = Team::find($teamId);
        }

        // Validate inputs
        $validated = $this->validate($inputs);

        $media = null;
        if (isset($validated['media']) && $validated['media']) {
            $media = $this->getMediaFromInput($teamId, $validated['media']);
        }
        if ($media) {
            abort_unless(
                MediaFileService::companyOwned($teamId, $media),
                403,
                trans('validation.403'),
            );
        }

        // Create `CatalogItemType`
        $catalogItemType = CreateCatalogItemType::run($user, $teamId, $validated['type']);

        $catalogItem->type()
            ->associate($catalogItemType);
        $catalogItem->author()
            ->associate($user);
        $catalogItem->company()
            ->associate($teamId);
        $catalogItem->media()
            ->associate($media);

        $unit = data_get($validated, 'unit');
        if (filled($unit)) {
            $unit = CatalogItemUnit::find($unit);

            if (blank($unit)) {
                throw new \Exception('Unit not found');
            }

            data_set($validated, 'unit_name', $unit->name);
        }

        return tap($catalogItem)->update(
            Arr::only($validated, [
                'name',
                'internal_id',
                'vendor_id',
                'manufacture_id',
                'unit',
                'unit_name',
                'purchasing_price',
                'selling_price',
                'description',
            ]),
        );
    }
}
