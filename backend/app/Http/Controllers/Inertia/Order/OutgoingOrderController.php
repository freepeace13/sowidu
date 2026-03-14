<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\GetOrderSummaries;
use App\Actions\Order\Outgoing\CreateOutgoingOrder;
use App\Actions\Order\Outgoing\DeleteOutgoingOrder;
use App\Actions\Order\Outgoing\UpdateOutgoingOrder;
use App\Enums\OrderStatus;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\WithOrderService;
use App\Models\Order;
use App\Traits\InteractsWithImpersonator;
use App\Transformers\Order\OrderTransformer;
use App\Transformers\PaginatorTransformer;
use App\Transformers\PlaceTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutgoingOrderController extends InertiaController
{
    use InteractsWithImpersonator, WithOrderService;

    public function index(Request $request)
    {
        $service = $this->createOrderService();

        $user = $request->user();
        $company = $this->getCurrentTeam();

        $requestFilters = $request->only([
            'status',
            'q',
            'dateAdded',
            'started',
            'plannedFinished',
        ]);

        $collection = $service
            ->outgoing()
            ->when(
                $request->has('sortBy'),
                fn ($query) => $query->orderBy('status', $request->get('descending') ? 'DESC' : 'ASC'),
                fn ($query) => $query->orderBy('created_at', 'DESC'),
            )
            ->with([
                'client',
                'deliveryAddress',
                'contractor',
            ])
            ->filter($requestFilters)
            ->paginate($request->get('rowsPerPage', 20));

        return Inertia::render('Order/OutgoingOrders', [
            'orders' => collect($collection->items())
                ->map(
                    fn ($order) => $service->transform($order),
                ),
            'filters' => $requestFilters,

            'orderStatuses' => Inertia::lazy(fn () => OrderStatus::asSelectItems()),

            'summaries' => fn () => GetOrderSummaries::run(
                $user,
                $company,
                $requestFilters),

            'paginator' => (new PaginatorTransformer($collection))->resolve(),

            'title' => trans('order.labels.incoming-orders'),

            'ownedPlaces' => Inertia::lazy(
                fn () => $this->account()
                    ->ownedPlaces()
                    ->private()
                    ->get()
                    ->map(
                        fn ($place) => (new PlaceTransformer($place))
                            ->withId()
                            ->withLabel()
                            ->resolve(),
                    ),
            ),
            'currentAddress' => fn () => (
                new PlaceTransformer(
                    $this->account()
                        ->currentPlace()
                        ->first(),
                )
            )->withId()
                ->withLabel()
                ->resolve(),
        ]);
    }

    public function store(Request $request, CreateOutgoingOrder $creator)
    {
        $creator->create($request->user(), $request->all(), $this->getCurrentTeam());

        return back(303);
    }

    public function show(Request $request, Order $order)
    {
        $service = $this->createOrderService();

        abort_unless(
            $service->isOrderedByCurrentUser($order),
            404,
            'Order not found!',
        );

        return Inertia::render('Order/Show', [
            'order' => (new OrderTransformer($order))
                ->withClientFullDetails($order->client)
                ->withDeliveryAddress()
                ->withContractorDetails()
                ->withRequiresResponse($request->user(), $this->getCurrentTeam())
                ->resolve(),
            'orderStatuses' => collect(OrderStatus::options())->map(fn ($value, $label) => [
                'value' => $value,
                'label' => snake_to_readable($label)->__toString(),
            ])
                ->values(),

            'ownedPlaces' => Inertia::lazy(
                fn () => $this->account()
                    ->ownedPlaces()
                    ->private()
                    ->get()
                    ->map(
                        fn ($place) => (new PlaceTransformer($place))
                            ->withId()
                            ->withLabel()
                            ->resolve(),

                    ),

            ),
            'currentAddress' => fn () => (
                new PlaceTransformer(
                    $this->account()
                        ->currentPlace()
                        ->first(),

                )
            )->withId()
                ->withLabel()
                ->resolve(),
        ]);
    }

    public function update(Request $request, Order $order, UpdateOutgoingOrder $updater)
    {
        $updater->update(
            $request->user(),
            $order,
            $request->all(),
            $this->getCurrentTeam(),
        );

        return back(303);
    }

    public function destroy(Request $request, Order $order, DeleteOutgoingOrder $action)
    {
        $action->delete($request->user(), $order, $this->getCurrentTeam());

        return back(303);
    }
}
