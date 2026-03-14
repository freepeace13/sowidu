<?php

namespace App\Transformers;

use App\Models\Invoice;
use App\Models\Order;
use App\Services\CacheService;
use App\Transformers\Addressbook\AddressbookTransformer;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Support\Collection;

/**
 * @property \App\Models\DeliveryTicket $resource
 */
class DeliveryTicketTransformer extends Transformer
{
    public function toArray($request)
    {
        $type = $this->resource->type;

        return [
            'id' => $this->resource->id,
            'internal_id' => $this->resource->internal_id,
            'external_id' => $this->resource->external_id,
            'delivery_date' => $this->resource->delivery_date->format('m-d-Y'),
            'deliverer_id' => $this->resource->deliverer_id,
            'type' => [
                'name' => snake_to_readable($type->name),
                'value' => $type->value,
            ],
            'total_purcashing_price' => $this->resource->total_purcashing_price,
            'total_selling_price' => $this->resource->total_selling_price,
        ];
    }

    public function withIsPaidStatus()
    {
        $isPaid = $this->resource->is_paid;
        $label = '--';

        if ($isPaid) {
            $label = trans('invoices.labels.paid');
        }

        return $this->state(fn () => [
            'is_paid' => $isPaid,
            'is_paid_label' => $label,
        ]);
    }

    public function withInvoicesStatus(Collection $invoices): self
    {
        return $this->state(fn () => [
            'invoices' => $invoices->map(
                fn ($invoice) => (new InvoiceTransformer($invoice))
                    ->withStatus()
                    ->resolve(),
            ),
        ]);
    }

    public function withInvoicedStatus(?Invoice $invoice)
    {
        return $this->state(fn () => [
            'invoice' => blank($invoice)
                ? [
                    'status' => [
                        'label' => trans('labels.un-invoiced'),
                        'value' => null,
                        'is_draft' => false,
                        'is_pending' => false,
                        'is_sent' => false,
                        'color' => 'default',
                        'icon' => 'unpublished',
                    ],
                ]
                : (new InvoiceTransformer($invoice))
                    ->withStatus()
                    ->resolve(),
        ]);
    }

    public function withDelivererDetails()
    {
        return $this->state(fn () => [
            'deliverer' => (new AddressbookTransformer($this->resource->loadMissing(['deliverer'])
                ->deliverer))->resolve(),
        ]);
    }

    public function withDelivererFullDetails()
    {
        return $this->state(fn () => [
            'deliverer' => (new AddressbookTransformer($this->resource->deliverer))
                ->withAddress()
                ->resolve(),
        ]);
    }

    public function withOrderDetails()
    {
        return $this->state(fn () => [
            'order' => [
                'id' => $this->resource->order->id,
                'order_number' => $this->resource->order->order_number,
            ],
        ]);
    }

    public function withOrderFullDetails(Order $order)
    {
        return $this->state(fn () => [
            'order' => (new OrderTransformer($order))
                ->withClientPrimaryDetails($order->client)
                ->withDeliveryAddress()
                ->resolve(),
        ]);
    }

    public function withDeliveryAddress()
    {
        return $this->state(fn () => [
            'delivery_address' => (new PlaceTransformer($this->resource->deliveryAddress))
                ->withId()
                ->resolve(),
        ]);
    }

    public function withDocuments()
    {
        return $this->state(fn () => [
            'documents' => $this->resource->documents
                ->map(
                    fn ($document) => array_merge(
                        (new MediaTransformer($document->media))
                            ->withCreatedAt()
                            ->resolve(),
                        [
                            'delivery_ticket_document_id' => $document->id,
                        ],
                    ),
                ),
        ]);
    }

    public function withTotalPurchasingPrice(Collection $materials): self
    {
        $totalPurchasingPrice = $materials
            ->sum(
                fn ($material) => $material->quantity * $material->details->get('purchasing_price', 0),
            );

        $currency = CacheService::getCompanyCurrency($this->resource->company_id);

        return $this->state(fn () => [
            'total_purchasing_price' => $totalPurchasingPrice,
            'total_purchasing_price_formatted' => number_to_money($totalPurchasingPrice, $currency),
        ]);
    }

    public function withTotalSellingPrice(Collection $materials): self
    {
        $totalSellingPrice = $materials
            ->sum(
                fn ($material) => $material->quantity * $material->details->get('selling_price', 0),
            );
        $currency = CacheService::getCompanyCurrency($this->resource->company_id);

        return $this->state(fn () => [
            'total_selling_price' => $totalSellingPrice,
            'total_selling_price_formatted' => number_to_money(
                $totalSellingPrice,
                $currency,
            ),
        ]);
    }
}
