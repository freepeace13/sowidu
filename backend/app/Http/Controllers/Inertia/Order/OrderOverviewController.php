<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\GetOrderSummaries;
use App\Enums\OrderStatus;
use App\Http\Controllers\Inertia\InertiaController;
use App\Http\Controllers\Traits\WithOrderService;
use App\Models\Order;
use App\Transformers\PaginatorTransformer;
use App\Transformers\PlaceTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderOverviewController extends InertiaController
{
    use WithOrderService;

    public function __invoke(Request $request)
    {
        $service = $this->createOrderService();
        $user = $request->user();
        $company = $this->getCurrentCompany();

        $requestFilters = $request->only([
            'status',
            'invoice',
            'q',
            'dateAdded',
            'started',
            'plannedFinished',
        ]);

        $collection = $service
            ->overview()
            ->withCount(['invoices'])
            ->when(
                $request->has('sortBy'),
                fn ($query) => $query->orderBy('status', $request->get('descending') ? 'DESC' : 'ASC'),
                fn ($query) => $query->orderBy('created_at', 'DESC'),
            )
            ->when(isset($requestFilters['invoice']) && $requestFilters['invoice'] == 'no_invoice',
                fn ($query) => $query->having('invoices_count', '=', 0),
            )
            ->when(isset($requestFilters['invoice']) && $requestFilters['invoice'] == 'with_invoice',
                fn ($query) => $query->having('invoices_count', '>', 0),
            )
            ->with([
                'client',
                'deliveryAddress',
                'contractor',
            ])
            ->filter($requestFilters)
            ->paginate($request->get('rowsPerPage', 20))->withQueryString();

        return Inertia::render('Order/Overview', [
            'title' => trans('order.titles.overview'),
            'orders' => collect($collection->items())
                ->map(
                    fn (Order $order) => $service->transform($order),
                ),

            'paginator' => (new PaginatorTransformer($collection))->resolve(),

            'filters' => $requestFilters,

            'orderStatuses' => fn () => OrderStatus::asSelectItems(),

            'summaries' => fn () => GetOrderSummaries::run(
                $user,
                $company,
                $requestFilters),

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
}
