<?php

namespace App\Actions\Order;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\User;
use App\Services\Order\OrderService;

class GetOrderSummaries
{
    use AsAction;

    public function handle(
        User $user,
        ?Company $company = null,
        array $filters = [],
    ): array {
        $service = OrderService::make($user, $company);

        $outgoingRequiresResponse = $service->countOfOutgoingRequiresResponse($filters);
        $incomingRequiresResponse = $service->countOfIncomingRequiresResponse($filters);

        $incomingCount = $service->countOfIncoming($filters);
        $outgoingCount = $service->countOfOutgoing($filters);

        return [
            'all' => [
                'total' => $incomingCount + $outgoingCount,
                'requires_response' => $outgoingRequiresResponse + $incomingRequiresResponse,
            ],
            'incoming' => [
                'total' => $incomingCount,
                'requires_response' => $incomingRequiresResponse,
            ],
            'outgoing' => [
                'total' => $outgoingCount,
                'requires_response' => $outgoingRequiresResponse,
            ],
        ];
    }
}
