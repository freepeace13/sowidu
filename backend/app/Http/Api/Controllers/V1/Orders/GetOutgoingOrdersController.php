<?php

namespace App\Http\Api\Controllers\V1\Orders;

use App\Contracts\Actions\RetrievesOutgoingOrders;
use App\Http\Api\Resources\V1\Orders\OrderResource;
use App\Http\Controllers\Traits\WithOrderService;
use Illuminate\Http\Request;
use Packages\RestApi\RestfulController;

class GetOutgoingOrdersController extends RestfulController
{
    use WithOrderService;

    public function __construct(
        protected RetrievesOutgoingOrders $outgoingOrders,
    ) {}

    public function __invoke(Request $request)
    {
        $user = $this->currentUser();
        $currentTeam = $this->currentTeam();

        $filters = $request->only([
            'status', 'q', 'dateAdded', 'started', 'plannedFinished',
        ]);

        $orders = $this->outgoingOrders->retrieve(
            user: $this->currentUser(),
            filters: $filters,
            sortBy: $request->get('sortBy'),
            perPage: $request->get('rowsPerPage', 20),
            teamId: $this->getCurrentTeamId(),
        );

        return OrderResource::collection(
            $orders,
            fn (OrderResource $resource) => $resource
                ->withDeliveryAddress()
                ->withStatus($user, $currentTeam)
                ->withType($user, $currentTeam),
        );
    }
}
