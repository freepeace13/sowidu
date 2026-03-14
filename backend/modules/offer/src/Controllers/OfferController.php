<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;
use Modules\Offer\Actions\CreateOffer;
use Modules\Offer\Actions\DeleteOffer;
use Modules\Offer\Actions\UpdateOffer;
use Modules\Offer\Contracts\External\CatalogServiceContract;
use Modules\Offer\Enums\OfferStatus;
use Modules\Offer\Enums\OfferType;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;
use Modules\Offer\Repositories\OfferRepository;
use Modules\Offer\Support\Vuetify\CreateOptions;
use Modules\Offer\Transformers\OfferItemTransformer;
use Modules\Offer\Transformers\OfferTransformer;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all([
            'q',
            'offerDate',
            'status',
            'type',
        ]);

        return Inertia::render('Index', [
            'filters' => $filters,

            'offers' => fn () => OfferRepository::make(
                $request->user(),
                $request->company(),
            )->filter($filters)
                ->latest()
                ->with(['recipientable'])
                ->paginate($request->get('perPage', 15))
                ->withQueryString()
                ->through(
                    fn ($offer) => OfferTransformer::make($offer)
                        ->withAmounts()
                        ->withStatusMetadata()
                        ->withRecipientInfo($offer->recipientable)
                        ->withAuthPermissions()
                        ->resolve(),
                ),

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
        $catalogService = app(CatalogServiceContract::class);

        $offer->load(['order:id,offer_id,order_number', 'constructionSite']);
        $service = OfferService::make($offer);
        $company = $offer->company;

        return Inertia::render('Show', [
            'offer' => OfferTransformer::make($offer)
                ->withAmounts()
                ->withTaxes(
                    $service->taxesAmounts(),
                )
                ->withStatusMetadata()
                ->withPdfMessage()
                ->withOrderInfo($offer?->order)
                ->withRecipientInfo($offer->recipient)
                ->withConstructionSite($offer->constructionSite)
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
                'can edit offer' => $user->can('edit', $offer),
                'can delete offer' => $user->can('delete', $offer),
                'can modify offer status' => $user->can('modifyStatus', $offer),
            ],

            'itemTypeOptions' => fn () => CreateOptions::from(
                CatalogService::make(
                    $user,
                    $company,
                )
                    ->allItemTypes(),
            )->build(),

            'unitOptions' => fn () => CreateOptions::from($catalogService->getAllUnitsForOptions())
                ->setTextKey('name')
                ->setValueKey('id')
                ->build(),

            'offerUrl' => Inertia::lazy(fn () => $offer->orderUrl()),
        ]);
    }

    public function update(Request $request, Offer $offer)
    {
        UpdateOffer::run(
            $request->user(),
            $request->company(),
            $offer,
            $request->all(),
        );

        flash_success(__('offer.messages.updated'));

        return back();
    }

    public function destroy(Request $request, Offer $offer)
    {
        DeleteOffer::run(
            request()->user(),
            request()->company(),
            $offer,
        );

        flash_success(__('offer.messages.deleted'));

        return to_route('offers.index');
    }
}
