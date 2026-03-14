<?php

namespace Modules\Catalog\Actions;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Catalog\Contracts\CreatesCatalogItem as CreatesCatalogItemContract;
use Modules\Catalog\Contracts\External\MediaManagerContract;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Services\CatalogService;

class CreateCatalogItem implements CreatesCatalogItemContract
{
    use AsAction;
    use AuthorizesRequests;
    use Concerns\ValidatesCatalogItems;

    public function handle(User $user, Company $company, array $inputs): CatalogItem
    {
        return $this->create($user, $inputs, $company->getKey());
    }

    public function create(User $user, array $inputs, $teamId = null, $errorBag = null): CatalogItem
    {
        $company = $teamId ? Company::find($teamId) : null;
        abort_unless($company, 404, 'Company not found');

        $this->authorizeForUser($user, 'create', [CatalogItem::class, $company]);

        // Validate inputs
        $validated = $this->validate($inputs);
        $media = null;
        if (array_key_exists('media', $validated)) {
            $media = $this->getMediaFromInput($company, $validated['media']);

            abort_unless(
                ($this->mediaManager ?? app(MediaManagerContract::class))
                    ->isOwnedByCompany($company, $media),
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
