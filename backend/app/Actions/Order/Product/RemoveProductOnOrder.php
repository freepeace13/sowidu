<?php

namespace App\Actions\Order\Product;

use App\Actions\Traits\AsAction;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class RemoveProductOnOrder
{
    use AsAction;

    public function handle(User $user, Company $company, Order $order, OrderProduct $orderProduct)
    {
        Gate::forUser($user)->authorize('manageProducts', $order);

        $this->deleteInvoiceItem($orderProduct);

        $orderProduct->delete();
    }

    public function deleteInvoiceItem(OrderProduct $orderProduct)
    {
        $invoiceItem = $orderProduct->invoiceItem()
            ->with(['invoice'])
            ->first();

        if (!$invoiceItem) {
            return;
        }

        $invoiceItem->delete();
    }
}
