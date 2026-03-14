<?php

namespace Modules\Offer\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;
use Modules\Offer\Actions\CreateOffer;
use Modules\Offer\Contracts\External\CatalogServiceContract;
use Modules\Offer\Contracts\External\OrderServiceContract;
use Modules\Offer\Enums\OfferStatus;
use Modules\Offer\Enums\OfferType;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;
use Modules\Offer\Repositories\OfferRepository;
use Modules\Offer\Support\Vuetify\CreateOptions;
use Modules\Offer\Transformers\OfferItemTransformer;
use Modules\Offer\Transformers\OfferTransformer;

class OrderOfferController extends Controller
{
    public function __construct(
        protected OrderServiceContract $orderService,
        protected CatalogServiceContract $catalogService,
    ) {}

    public function index(Request $request, Model $order)
    {
        $user = $request->user();

        $filters = $request->all([
            'q',
            'offerDate',
            'status',
            'type',
        ]);

        return Inertia::render('Order/Offer/Index', [
            'filters' => $filters,

            'order' => $this->orderService->transformWithOfferDetails($order),

            'offers' => fn () => OfferRepository::make(
                $request->user(),
                $request->company(),
            )->filter($filters)
                ->where('order_id', $order->id)
                ->latest()
                ->with(['recipientable'])
                ->get()
                ->map(
                    fn ($offer) => OfferTransformer::make($offer)
                        ->withAmounts()
                        ->withStatusMetadata()
                        ->withRecipientInfo($offer->recipientable)
                        ->withAuthPermissions()
                        ->resolve(),
                ),

            'permissions' => [
                'can_create_offer' => $user->can('create', Offer::class),
            ],

            'offerTypes' => fn () => collect(
                CreateOptions::from(OfferType::options())->build(),
            )->values()
                ->toArray(),

            'offerStatuses' => fn () => collect(
                CreateOptions::from(OfferStatus::options())->build(),
            )->values()
                ->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        CreateOffer::run(
            $request->user(),
            $request->company(),
            $request->all(),
        );

        flash_success(__('offer.messages.created'));

        return back();
    }

    public function show(Request $request, Offer $offer)
    {
        $user = $request->user();

        $offer->load(['order:id,offer_id,order_number']);
        $company = $offer->company;

        return Inertia::render('Order/Show', [
            'offer' => OfferTransformer::make($offer)
                ->withAmounts()
                ->withStatusMetadata()
                ->withPdfMessage()
                ->withOrderInfo($offer?->order)
                ->withRecipientInfo($offer->recipient)
                ->resolve(),

            'items' => fn () => $offer->items()
                ->get()
                ->map(
                    fn ($item) => OfferItemTransformer::make($item)
                        ->withDetails()
                        ->withCurrency($company->currency)
                        ->resolve(),
                ),

            'permissions' => [
                'can edit offer' => false,
                'can delete offer' => false,
                'can modify offer status' => $user->can('modifyStatus', $offer),
                'can accept offer' => $user->can('accept', $offer),
                'can reject offer' => $user->can('reject', $offer),
                'can cancel offer' => false,
            ],

            'itemTypeOptions' => fn () => CreateOptions::from(
                CatalogService::make(
                    $user,
                    $company,
                )
                    ->allItemTypes(),
            )->build(),

            'unitOptions' => fn () => CreateOptions::from($this->catalogService->getAllUnitsForOptions())
                ->setTextKey('name')
                ->setValueKey('id')
                ->build(),

            'offerUrl' => Inertia::lazy(fn () => $offer->orderUrl()),
        ]);
    }
}
