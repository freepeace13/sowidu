<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Enums\DeliveryTicketType;
use App\Http\Controllers\Controller;
use App\Models\CatalogItemUnit;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\CacheService;
use App\Services\DeliveryTicketsService;
use App\Support\Vuetify\CreateOptions;
use App\Transformers\DeliveryTicketMaterialTransformer;
use App\Transformers\DeliveryTicketTransformer;
use App\Transformers\MediaTransformer;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;

class OrderDeliveryTicketController extends Controller
{
    public function __construct(public Pipeline $pipeline) {}

    public function index(Request $request, Order $order)
    {
        $user = $request->user();

        abort_unless(
            $user->can('accessDeliveryTickets', $order),
            403,
            trans('validation.403'),
        );
        $deliveryTickets = OrderRepository::make(
            $user,
            $request->company(),
        )
            ->onOrder($order)
            ->deliveryTickets();

        return Inertia::render(
            'Order/DeliveryTicket/Index',
            [
                'order' => OrderTransformer::make($order)
                    ->withClientPrimaryDetails($order->client)
                    ->withDeliveryAddress()
                    ->resolve(),
                'totalPurchasingPrice' => number_to_money($deliveryTickets->sum('total_purchasing_price') ?? 0),
                'totalSellingPrice' => number_to_money($deliveryTickets->sum('total_selling_price')),
                'deliveryTickets' => $deliveryTickets
                    ->map(
                        fn (DeliveryTicket $deliveryTicket) => DeliveryTicketTransformer::make($deliveryTicket)
                            ->withIsPaidStatus()
                            ->withInvoicesStatus($deliveryTicket->invoices)
                            ->withDelivererDetails()
                            ->withTotalPurchasingPrice($deliveryTicket->materials)
                            ->withTotalSellingPrice($deliveryTicket->materials)
                            ->resolve(),
                    ),
                'permissions' => [
                    'can_manage_order_delivery_tickets' => $user
                        ->can('manageDeliveryTickets', $order),
                ],

                'deliveryTicketTypes' => fn () => collect(
                    CreateOptions::from(DeliveryTicketType::options())->build(),
                )
                    ->values()
                    ->toArray(),

            ],
        );
    }

    public function show(Request $request, Order $order, DeliveryTicket $deliveryTicket)
    {
        $user = $request->user();

        abort_unless(
            $user->can('accessDeliveryTickets', $order),
            403,
            trans('validation.403'),
        );

        $company = $request->company();

        $deliveryTicket->loadMissing(['order']);

        $currency = CacheService::getCompanyCurrency($deliveryTicket->company);

        return Inertia::render('Order/DeliveryTicket/Show', [
            'deliveryTicket' => DeliveryTicketTransformer::make($deliveryTicket)
                ->withIsPaidStatus()
                ->withDelivererDetails()
                ->withDeliveryAddress()
                ->withOrderFullDetails($deliveryTicket->order)
                ->resolve(),
            'documents' => $deliveryTicket->documents()
                ->get()
                ->map(
                    fn ($document) => array_merge(
                        (new MediaTransformer($document->media))
                            ->withCreatedAt()
                            ->resolve(),
                        [
                            'delivery_ticket_document_id' => $document->id,
                        ],
                    ),
                ),

            'order' => OrderTransformer::make($order)->resolve(),

            'permissions' => [
                'can_update_ticket' => $user->can('manageMaterials', $deliveryTicket)
                    && $deliveryTicket->isUnPaid(),
                'can_update_prices' => $user->can('manageMaterials', $deliveryTicket) && $deliveryTicket->isUnPaid(),
            ],

            'materials' => $deliveryTicket
                ->materials()
                ->get()
                ->map(
                    fn (DeliveryTicketMaterial $material) => DeliveryTicketMaterialTransformer::make($material)
                        ->withPurchasingPrice($currency)
                        ->withSellingPrice($currency)
                        ->resolve(),
                ),

            'itemTypeOptions' => fn () => CreateOptions::from(
                CatalogService::make($user, $company)
                    ->allItemTypes(),
            )->build(),

            'unitOptions' => fn () => CreateOptions::from(CatalogItemUnit::all())
                ->setTextKey('name')
                ->setValueKey('id')
                ->build(),

            'totals' => fn () => DeliveryTicketsService::make(
                $user,
                $company,
            )->getTotalPurchasingAndSellingPrices($deliveryTicket),

        ]);
    }

    public function import(Order $order, Request $request): RedirectResponse
    {

        return $this
            ->pipeline
            ->send($request)
            ->through(config('pipes.deliveryTicket.import'))
            ->then(fn () => back());
    }

    public function update(Request $request, Order $order, DeliveryTicket $deliveryTicket): RedirectResponse
    {

        $deliveryTicket->update([
            'deliverer_id' => $request->input('deliverer'),
        ]);

        return back();
    }
}
