<?php

namespace App\Http\Controllers\Inertia\DeliveryTicket;

use App\Actions\DeliveryTicket\CreateDeliveryTicket;
use App\Actions\DeliveryTicket\DeleteDeliveryTicket;
use App\Actions\DeliveryTicket\UpdateDeliveryTicket;
use App\Enums\DeliveryTicketType;
use App\Http\Controllers\Controller;
use App\Models\CatalogItemUnit;
use App\Models\DeliveryTicket;
use App\Services\CacheService;
use App\Services\CompanyService;
use App\Services\DeliveryTicketsService;
use App\Services\MediaFileService;
use App\Support\Vuetify\CreateOptions;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\DeliveryTicketMaterialTransformer;
use App\Transformers\DeliveryTicketTransformer;
use App\Transformers\MediaTransformer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;

class DeliveryTicketController extends Controller
{
    use InteractsWithImpersonator;

    public function index(Request $request)
    {
        return Inertia::render('DeliveryTicket/Index', [
            'allowedTypes' => fn () => Arr::flatten(
                MediaFileService::allowedMimetypes(),
            ),

            'deliveryTicketTypes' => fn () => collect(
                CreateOptions::from(DeliveryTicketType::options())->build(),
            )
                ->values()
                ->toArray(),
            'itemTypeOptions' => fn () => CreateOptions::from(
                CatalogService::make($request->user(), $this->getCurrentCompany())
                    ->allItemTypes(),
            )->build(),

            'unitOptions' => fn () => CreateOptions::from(CatalogItemUnit::all())
                ->setTextKey('name')
                ->setValueKey('id')
                ->build(),

            'companyCurrency' => CompanyService::make($request->company())->currency(),
        ]);
    }

    public function show(Request $request, DeliveryTicket $deliveryTicket)
    {
        abort_if(
            !$deliveryTicket->isOwnedByCompany($request->company()),
            403,
            trans('validation.403'),
        );

        $user = $request->user();
        $company = $request->company();

        $currency = CacheService::getCompanyCurrency($deliveryTicket->company);

        return Inertia::render('DeliveryTicket/Show', [
            'deliveryTicket' => DeliveryTicketTransformer::make($deliveryTicket->loadMissing([
                'order',
            ]))
                ->withDelivererDetails()
                ->withDelivererFullDetails()
                ->withOrderFullDetails($deliveryTicket->order)
                ->withDeliveryAddress()
                ->resolve(),
            'documents' => fn () => $deliveryTicket->loadMissing(['documents.media'])
                ->documents->map(
                    fn ($document) => array_merge(
                        (new MediaTransformer($document->media))
                            ->withCreatedAt()
                            ->resolve(),
                        [
                            'delivery_ticket_document_id' => $document->id,
                        ],
                    ),
                ),
            'materials' => fn () => $deliveryTicket->loadMissing(['materials'])
                ->materials
                ->map(
                    fn ($material) => DeliveryTicketMaterialTransformer::make($material)
                        ->withPurchasingPrice($currency)
                        ->withSellingPrice($currency)
                        ->withCurrency(
                            CompanyService::make($company)->currency(),
                        )
                        ->resolve(),
                ),
            'allowedTypes' => fn () => Arr::flatten(
                MediaFileService::allowedMimetypes(),
            ),

            'totals' => fn () => DeliveryTicketsService::make(
                $user,
                $company,
            )->getTotalPurchasingAndSellingPrices(
                $deliveryTicket,
            ),

            'permissions' => fn () => [
                'manage_materials' => $user->can('manageMaterials', $deliveryTicket) && $deliveryTicket->isUnPaid(),
            ],

            'itemTypeOptions' => fn () => CreateOptions::from(
                CatalogService::make($user, $this->getCurrentCompany())
                    ->allItemTypes(),
            )->build(),

            'unitOptions' => fn () => CreateOptions::from(CatalogItemUnit::all())
                ->setTextKey('name')
                ->setValueKey('id')
                ->build(),
        ]);

    }

    public function store(Request $request)
    {
        CreateDeliveryTicket::run($request->user(), $request->company(), $request->all());

        flash_success(trans('delivery_tickets.message.success.creating'));

        return back();
    }

    public function update(Request $request, DeliveryTicket $deliveryTicket)
    {
        UpdateDeliveryTicket::run($request->user(), $request->company(), $deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.success.updating'));

        return back();
    }

    public function deliveryAddressUpdate(Request $request, DeliveryTicket $deliveryTicket): RedirectResponse
    {

        $deliveryTicket->update([
            'delivery_address_id' => $request->input('delivery_address'),
            'deliverer_id' => $request->input('deliverer'),
        ]);

        flash_success(trans('delivery_tickets.message.success.updating'));

        return back();
    }

    public function destroy(Request $request, DeliveryTicket $deliveryTicket)
    {
        DeleteDeliveryTicket::run($request->user(), $request->company(), $deliveryTicket);

        flash_success(trans('delivery_tickets.message.success.deleting'));

        return back();
    }
}
