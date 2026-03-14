<?php

namespace App\Http\Controllers\Json\Catalog;

use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Http\Request;
use Modules\Catalog\Repositories\CatalogItemRepository;
use Modules\Catalog\Transformers\CatalogItemTransformer;
use Modules\Shared\Enums\Permissions;

class GetCatalogItemController extends Controller
{
    use InteractsWithImpersonator;

    public function __invoke(Request $request)
    {
        return response()->json(
            CatalogItemRepository::make($request->user(), $this->getCurrentCompany())
                ->filter($request->only(['q', 'type']))
                ->with(['media', 'type'])
                ->paginate(10)
                ->through(
                    fn ($catalogItem) => (new CatalogItemTransformer($catalogItem))
                        ->withMedia($catalogItem->media)
                        ->withType($catalogItem->type)
                        ->withSellingPrice(
                            PermissionService::allows(Permissions::CAN_VIEW_SELLING_PRICE),
                        )
                        ->withPurchasingPrice(
                            PermissionService::allows(Permissions::CAN_VIEW_PURCHASING_PRICE),
                        )
                        ->resolve(),
                ),
        );
    }
}
