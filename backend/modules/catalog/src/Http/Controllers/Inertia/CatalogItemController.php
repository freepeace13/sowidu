<?php

namespace Modules\Catalog\Http\Controllers\Inertia;

use App\Http\Controllers\Inertia\InertiaController as BaseInertiaController;
use App\Support\Vuetify\CreateOptions;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Catalog\Actions\CreateCatalogItem;
use Modules\Catalog\Actions\DeleteCatalogItem;
use Modules\Catalog\Actions\UpdateCatalogItem;
use Modules\Catalog\Contracts\External\CompanyInfoContract;
use Modules\Catalog\Contracts\External\PermissionContract;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemUnit;
use Modules\Catalog\Repositories\CatalogItemRepository;
use Modules\Catalog\Services\CatalogService;
use Modules\Catalog\Transformers\CatalogItemTransformer;
use Modules\Shared\Enums\Permissions;

class CatalogItemController extends BaseInertiaController
{
    use InteractsWithImpersonator;

    public function __construct(
        protected PermissionContract $permissions,
        protected CompanyInfoContract $companyInfo,
    ) {}

    public function index(Request $request)
    {
        /** @todo Catalogs is also accessible to private account users. */

        return Inertia::render('@catalog/Index', [
            'items' => CatalogItemRepository::make($request->user(), $this->getCurrentCompany())
                ->filter($request->only(['q', 'type']))
                ->with(['media', 'type'])
                ->paginate(15)
                ->through(
                    fn ($catalogItem) => (new CatalogItemTransformer($catalogItem))
                        ->withMedia($catalogItem->media)
                        ->withSellingPrice(
                            $this->permissions->allows(Permissions::CAN_VIEW_SELLING_PRICE),
                        )
                        ->withPurchasingPrice(
                            $this->permissions->allows(Permissions::CAN_VIEW_PURCHASING_PRICE),
                        )
                        ->withType($catalogItem->type)
                        ->resolve(),
                ),

            'itemTypeOptions' => fn () => CreateOptions::from(
                CatalogService::make($request->user(), $this->getCurrentCompany())
                    ->allItemTypes(),
            )->build(),

            'unitOptions' => fn () => CreateOptions::from(CatalogItemUnit::all())
                ->setTextKey('name')
                ->setValueKey('id')
                ->build(),

            'filters' => $request->only(['q', 'type']),

            'currency' => fn () => $this->companyInfo->currency($this->getCurrentCompany()),
        ]);
    }

    public function store(Request $request)
    {
        CreateCatalogItem::run($request->user(), $this->getCurrentCompany(), $request->all());

        flash_success(trans('catalog.notifications.success.create-item'));

        return back();
    }

    public function update(Request $request, CatalogItem $item)
    {
        UpdateCatalogItem::run($request->user(), $this->getCurrentCompany(), $item, $request->all());

        flash_success(trans('catalog.notifications.success.update-item'));

        return back();
    }

    public function destroy(Request $request, CatalogItem $item)
    {
        DeleteCatalogItem::run($request->user(), $this->getCurrentCompany(), $item);

        flash_success(trans('catalog.notifications.success.delete-item'));

        return back();
    }
}
