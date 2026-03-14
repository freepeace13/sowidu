<?php

namespace App\Http\Api\Actions\Orders;

use App\Contracts\Actions\RetrievesIncomingOrders;
use App\Models\Company as Team;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Support\Arr;

class RetrieveIncomingOrders implements RetrievesIncomingOrders
{
    public function retrieve(User $user, array $filters, string $sortBy = 'DESC', int $perPage = 20, $teamId = null)
    {
        $orderService = OrderService::make($user, $teamId ? Team::find($teamId) : null);

        $applyFilters = Arr::only($filters, [
            'status',
            'q',
            'dateAdded',
            'started',
            'plannedFinished',
        ]);

        return $orderService
            ->incoming()
            ->orderBy('status', $sortBy)
            ->orderBy('created_at', $sortBy)
            ->with(['client', 'deliveryAddress', 'contractor'])
            ->filter($applyFilters)
            ->paginate($perPage);
    }
}
