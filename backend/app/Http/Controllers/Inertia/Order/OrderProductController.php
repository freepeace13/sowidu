<?php

namespace App\Http\Controllers\Inertia\Order;

use App\Actions\Order\Product\AddDeliveryTicketMaterialsToOrder;
use App\Actions\Order\Product\AddProductToOrder;
use App\Actions\Order\Product\RemoveProductOnOrder;
use App\Actions\Order\Product\UpdateProductQuantityOnOrder;
use App\Http\Controllers\Inertia\InertiaController;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Services\Order\OrderService;
use App\Services\Order\OrderViewerService;
use App\Support\Vuetify\CreateOptions;
use App\Transformers\Order\OrderProductTransformer;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Catalog\Services\CatalogService;

class OrderProductController extends InertiaController
{
    public function index(Request $request, Order $order)
    {
        $user = $request->user();
        $company = $request->company();

        $products = $order
            ->products()
            ->with(['invoiceItem.invoice', 'deliveryTicketMaterial'])
            ->get();

        $orderViewerService = OrderViewerService::make($order)->viewer($user, $company);

        return Inertia::render('Order/Products/OrderProducts', [
            'order' => fn () => (new OrderTransformer($order))->resolve(),

            'itemTypeOptions' => fn () => blank($company) ? [] : CreateOptions::from(
                CatalogService::make($user, $company)
                    ->allItemTypes(),
            )
                ->build(),

            'products' => $products
                ->map(
                    fn ($orderProduct) => (new OrderProductTransformer($orderProduct))
                        ->withInvoiceStatus($orderProduct->getInvoice())
                        ->withSellingPrice($orderViewerService->canViewSellingPrice())
                        ->withPurchasingPrice($orderViewerService->canViewPurchasingPrice())
                        ->resolve(),
                ),

            'totalPrice' => round(
                $products->reduce(
                    fn ($total, $product) => $total + (
                        $product->quantity * $product->details->purchasing_price),
                ),
                2,
            ),

            'permissions' => [
                'can_update_used_products' => $user
                    ->can('manageProducts', $order),
                'is_contractor' => OrderService::make($user, $company)->isContractor($order),
            ],
        ]);
    }

    public function store(Request $request, Order $order)
    {
        if ($request->get('is_delivery_ticket_materials', false)) {
            AddDeliveryTicketMaterialsToOrder::run(
                $request->user(),
                $request->company(),
                $order,
                $request->all(),
            );

            flash_success(trans('order.notifications.delivery-ticket-materials.added'));
        } else {
            AddProductToOrder::run(
                $request->user(),
                $request->company(),
                $order,
                $request->all(),
            );

            flash_success(trans('order.notifications.product.added'));
        }

        return back();
    }

    public function update(Request $request, Order $order, OrderProduct $orderProduct)
    {
        UpdateProductQuantityOnOrder::run(
            $request->user(),
            $request->company(),
            $order,
            $orderProduct,
            $request->all(),
        );

        flash_success(trans('order.notifications.product.updated'));

        return back();
    }

    public function destroy(Request $request, Order $order, OrderProduct $orderProduct)
    {
        RemoveProductOnOrder::run($request->user(), $request->company(), $order, $orderProduct);

        flash_success(trans('order.notifications.product.removed'));

        return back();
    }
}
