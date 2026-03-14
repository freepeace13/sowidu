<?php

namespace Modules\DeliveryTicket\Http\Controllers\Inertia;

use App\Enums\DeliveryTicketType;
use App\Http\Controllers\Controller;
use App\Models\CatalogItemUnit;
use App\Services\CacheService;
use App\Services\CatalogService;
use App\Services\CompanyService;
use App\Services\MediaFileService;
use App\Support\Vuetify\CreateOptions;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\DeliveryTicketMaterialTransformer;
use App\Transformers\DeliveryTicketTransformer;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Modules\DeliveryTicket\Contracts\Actions\CreateDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\DeleteDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDelivererTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryAddressTicketContract;
use Modules\DeliveryTicket\Contracts\Actions\UpdateDeliveryTicketContract;
use Modules\DeliveryTicket\Contracts\Services\DeliveryTicketsServiceContract;
use Modules\DeliveryTicket\Models\DeliveryTicket;

class DeliveryTicketController extends Controller
{
    use InteractsWithImpersonator;

    public function __construct(
        protected DeliveryTicketsServiceContract $deliveryTicketService,
        protected CreateDeliveryTicketContract $createDeliveryTicketAction,
        protected UpdateDeliveryTicketContract $updateDeliveryTicketAction,
        protected DeleteDeliveryTicketContract $deleteDeliveryTicketAction,
        protected UpdateDeliveryAddressTicketContract $updateDeliveryAddressTicketAction,
        protected UpdateDelivererTicketContract $updateDelivererTicketAction,
    ) {
        $this->deliveryTicketService = $deliveryTicketService;
        $this->createDeliveryTicketAction = $createDeliveryTicketAction;
        $this->updateDeliveryAddressTicketAction = $updateDeliveryAddressTicketAction;
        $this->updateDelivererTicketAction = $updateDelivererTicketAction;
        $this->updateDeliveryTicketAction = $updateDeliveryTicketAction;
        $this->deleteDeliveryTicketAction = $deleteDeliveryTicketAction;
    }

    public function index(Request $request)
    {
        return Inertia::render('Index', [
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

        return Inertia::render('Show', [
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

            'totals' => fn () => $this->deliveryTicketService->make(
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
        $this->createDeliveryTicketAction->handle($request->user(), $request->company(), $request->all());

        flash_success(trans('delivery_tickets.message.success.creating'));

        return back();
    }

    public function update(Request $request, DeliveryTicket $deliveryTicket)
    {
        $this->updateDeliveryTicketAction->handle($request->user(), $request->company(), $deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.success.updating'));

        return back();
    }

    public function deliveryAddressUpdate(Request $request, DeliveryTicket $deliveryTicket)
    {
        $this->updateDeliveryAddressTicketAction->handle($deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.success.updating'));

        return back();
    }

    public function delivererUpdate(Request $request, DeliveryTicket $deliveryTicket)
    {
        $this->updateDelivererTicketAction->handle($deliveryTicket, $request->all());

        flash_success(trans('delivery_tickets.message.success.updating'));
    }

    public function destroy(Request $request, DeliveryTicket $deliveryTicket)
    {
        $this->deleteDeliveryTicketAction->handle($request->user(), $request->company(), $deliveryTicket);

        flash_success(trans('delivery_tickets.message.success.deleting'));

        return back();
    }
}
