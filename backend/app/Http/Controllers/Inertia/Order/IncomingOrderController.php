<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\GetOrderSummaries;
use App\Actions\Order\Incoming\CreateIncomingOrder;
use App\Actions\Order\Incoming\DeleteIncomingOrder;
use App\Actions\Order\Incoming\UpdateIncomingOrder;
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

class IncomingOrderController extends InertiaController
{
    use InteractsWithImpersonator, WithOrderService;

    public function index(Request $request)
    {
        if (!$this->isImpersonating()) {
            return redirect()->route('orders.outgoing.index');
        }

        $service = $this->createOrderService();
        $requestFilters = $request->only([
            'status',
            'q',
            'dateAdded',
            'started',
            'plannedFinished',
        ]);

        $user = $request->user();
        $company = $this->getCurrentTeam();

        $collection = $service
            ->incoming()
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

        return Inertia::render('Order/IncomingOrders', [
            'orders' => collect($collection->items())
                ->map(
                    fn ($order) => $service->transform($order),
                ),
            'filters' => $requestFilters,

            'orderStatuses' => Inertia::lazy(fn () => OrderStatus::asSelectItems()),

            'summaries' => fn () => GetOrderSummaries::run($user, $company, $requestFilters),

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

    public function store(Request $request, CreateIncomingOrder $creator)
    {
        $creator->create($request->user(), $request->all(), $this->getCurrentTeam());

        return back(303);
    }

    public function update(Request $request, Order $order, UpdateIncomingOrder $updater)
    {
        $updater->update(
            $request->user(),
            $order,
            $request->all(),
            $this->getCurrentTeam(),
        );

        flash_success('Client on this order has been updated.');

        return back(303);
    }

    public function show(Request $request, Order $order)
    {
        $service = $this->createOrderService();

        abort_unless($service->isCurrentlyOwned($order), 404, 'Order not found!');

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
        ]);
    }

    public function destroy(Request $request, Order $order, DeleteIncomingOrder $action)
    {
        $action->delete($request->user(), $order, $this->getCurrentTeam());

        return back(303);
    }
}
