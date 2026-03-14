<?php

namespace App\Actions\Catalog;

use App\Actions\Traits\AsAction;
use App\Models\CatalogItem;
use App\Models\CatalogItemType;
use App\Models\Company;
use App\Models\User;
use App\Services\MediaFileService;
use Illuminate\Support\Facades\Gate;
use Modules\Catalog\Services\CatalogService;

class CreateCatalogItem
{
    use AsAction;
    use CatalogItemValidation;

    public function handle(User $user, Company $company, array $inputs): CatalogItem
    {
        // Check if this user can create a `catalog_item`
        Gate::forUser($user)->authorize('create', CatalogItem::class);

        // Validate inputs
        $validated = $this->validate($inputs);
        $media = null;
        if (array_key_exists('media', $validated)) {
            $media = $this->getMediaFromInput($company, $validated['media']);

            abort_unless(
                MediaFileService::companyOwned($company, $media),
                403,
                trans('validation.403'),
            );
        }

        // Create `CatalogItemType`
        $catalogItemType = CreateCatalogItemType::run($user, $company, $validated['type']);

        $catalogItem = CatalogService::make($user, $company)
            ->createItem($catalogItemType, $validated, $media);

        return $catalogItem;
    }
}
