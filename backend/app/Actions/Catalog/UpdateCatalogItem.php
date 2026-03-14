<?php

namespace App\Actions\Catalog;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use App\Models\Company;
use App\Models\User;
use App\Services\MediaFileService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class UpdateCatalogItem
{
    use AsAction;
    use CatalogItemValidation;

    public function handle(
        User $user,
        Company $company,
        CatalogItem $catalogItem,
        array $inputs,
    ): CatalogItem {
        // Validate if user has authorization to make this action
        Gate::forUser($user)->authorize('update', $catalogItem);

        // Validate inputs
        $validated = $this->validate($inputs);

        $media = $this->getMediaFromInput($company, $validated['media']);
        if ($media) {
            abort_unless(
                MediaFileService::companyOwned($company, $media),
                403,
                trans('validation.403'),
            );
        }

        // Create `CatalogItemType`
        $catalogItemType = CreateCatalogItemType::run($user, $company, $validated['type']);

        $catalogItem->type()
            ->associate($catalogItemType);
        $catalogItem->author()
            ->associate($user);
        $catalogItem->company()
            ->associate($company);
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
