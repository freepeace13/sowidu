<?php

namespace Modules\Catalog\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use App\Services\MediaFileService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Contracts\UpdatesCatalogItem as UpdatesCatalogItemContract;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemUnit;

class UpdateCatalogItem implements UpdatesCatalogItemContract
{
    use AsAction;
    use Concerns\ValidatesCatalogItems;

    public function handle(User $user, Company $company, CatalogItem $catalogItem, array $inputs): CatalogItem
    {
        return $this->update($user, $catalogItem, $inputs, $company->getKey());
    }

    public function update(User $user, CatalogItem $catalogItem, array $inputs, $teamId = null, $errorBag = null): CatalogItem
    {
        $company = $teamId ? Company::find($teamId) : null;
        abort_unless($company, 404, 'Company not found');

        // Validate if user has authorization to make this action
        Gate::forUser($user)->authorize('update', [$catalogItem, $teamId]);

        // Validate inputs
        $validated = $this->validate($inputs);

        // Create `CatalogItemType`
        $catalogItemType = CreateCatalogItemType::run($user, $company, $validated['type']);

        $catalogItem->type()
            ->associate($catalogItemType);
        $catalogItem->author()
            ->associate($user);
        $catalogItem->company()
            ->associate($company);

        if (isset($validated['media'])) {
            $media = $this->getMediaFromInput($company, $validated['media']);

            if ($media) {
                abort_unless(
                    MediaFileService::companyOwned($company, $media),
                    403,
                    trans('validation.403'),
                );
            }

            $catalogItem->media()->associate($media);
        }

        $unit = data_get($validated, 'unit');
        if (filled($unit)) {
            $unit = CatalogItemUnit::find($unit);

            if (blank($unit)) {
                throw new \Exception('Unit not found');
            }

            data_set($validated, 'unit_name', $unit->name);
        }

        // Get update attributes
        $updateAttributes = Arr::only($validated, [
            'name',
            'manufacture_id',
            'unit',
            'unit_name',
            'purchasing_price',
            'selling_price',
            'description',
        ]);

        // Include internal_id and vendor_id if provided, otherwise keep existing values
        // (internal_id is required, so we must have a value - either from input or keep existing)
        if (isset($validated['internal_id']) && !is_null($validated['internal_id'])) {
            $updateAttributes['internal_id'] = $validated['internal_id'];
        }
        // If not provided, keep the existing internal_id (don't update it)

        if (isset($validated['vendor_id']) && !is_null($validated['vendor_id'])) {
            $updateAttributes['vendor_id'] = $validated['vendor_id'];
        }
        // If vendor_id not provided, it can remain null (it's nullable in DB)

        return tap($catalogItem)->update($updateAttributes);
    }
}
