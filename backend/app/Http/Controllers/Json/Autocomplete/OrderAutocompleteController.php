<?php

namespace App\Http\Controllers\Json\Autocomplete;

use App\Http\Controllers\Json\BaseController;
use App\Models\Order;
use App\Services\Order\OrderService;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Http\Request;

class OrderAutocompleteController extends BaseController
{
    public function __invoke(Request $request)
    {
        // dd($request->all());
        return $this->json(
            OrderService::make(
                $request->user(),
                $request->company(),
            )
                ->incoming()
                ->with([
                    'client',
                    'deliveryAddress',
                    'contractor',
                ])
                ->when(
                    $notInStatuses = $request->get('not_in_statuses'),
                    fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNotIn('status', $notInStatuses),
                )
                ->filters(
                    $request->only([
                        'q',
                    ]),
                )
                ->limit($request->get(
                    'limit',
                    10,
                ))
                ->get()
                ->map(
                    function (Order $order) {
                        return (new OrderTransformer($order))
                            ->withClientPrimaryDetails($order->client)
                            ->withDeliveryAddress()
                            ->withContractorPrimaryDetails($order->contractor)
                            ->resolve();
                    },
                ),
        );
    }
}
