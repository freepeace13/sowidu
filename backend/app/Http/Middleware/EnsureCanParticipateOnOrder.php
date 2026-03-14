<?php

namespace App\Http\Middleware;

use App\Services\Order\OrderService;
use Closure;
use Illuminate\Http\Request;
use Impersonate;

class EnsureCanParticipateOnOrder
{
    public function handle(Request $request, Closure $next)
    {
        $order = $request->route('order');
        if (!$order) {
            return redirect()->route('orders.outgoing.index');
        }

        $orderService = OrderService::make($request->user(), Impersonate::tenant());
        if (!$orderService->isInvolvedOnOrder($order)) {
            return redirect()->route('orders.outgoing.index');
        }

        return $next($request);
    }
}
