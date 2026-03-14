<?php

namespace App\Transformers\Order;

use App\Models\Invoice;
use App\Transformers\InvoiceTransformer;
use App\Transformers\Transformer;

/**
 * @property \App\Models\OrderProduct $resource
 */
class OrderProductTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'quantity' => $this->resource->quantity,
            'is_paid' => $this->resource->is_paid,
            'details' => $this->resource->details,
            'is_delivery_ticket_materials' => $this->resource->isDeliveryTicketMaterial(),
            'status_label' => $this->resource->isDeliveryTicketMaterial() ? trans('delivery_tickets.labels.delivery-ticket-materials') : trans('order.labels.used-products'),
        ];
    }

    public function withInvoiceStatus(?Invoice $invoice)
    {
        return $this->state(
            fn () => [
                'invoice' => blank($invoice) ? [
                    'status' => [
                        'label' => trans('labels.un-invoiced'),
                        'value' => null,
                        'is_draft' => false,
                        'is_pending' => false,
                        'is_sent' => false,
                        'color' => 'default',
                        'icon' => 'unpublished',
                    ],
                ] : InvoiceTransformer::make($invoice)->withStatus()
                    ->resolve(),
            ],
        );
    }

    public function withSellingPrice(bool $canViewSellingPrice)
    {
        return $this->state(function ($attributes) use ($canViewSellingPrice) {
            if (!$canViewSellingPrice) {
                data_set($attributes, 'details.selling_price', '--');
            }

            return $attributes;
        });
    }

    public function withPurchasingPrice(bool $canViewPurchasingPrice)
    {
        return $this->state(function ($attributes) use ($canViewPurchasingPrice) {
            if (!$canViewPurchasingPrice) {
                data_set($attributes, 'details.purchasing_price', '--');
            }

            return $attributes;
        });
    }
}
