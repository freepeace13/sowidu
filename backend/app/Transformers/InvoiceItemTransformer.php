<?php

namespace App\Transformers;

use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use App\Models\InvoiceManualItem;
use App\Models\OrderProduct;
use App\Models\User;
use App\Modules\Invoice\Services\InvoiceItemService;
use App\Services\CompanyService;
use Modules\Invoicify\Models\InvoiceManualItem as ModelsInvoiceManualItem;
use Modules\WorkLogs\Models\WorkLog;

/**
 * @property \App\Models\InvoiceItem $resource
 */
class InvoiceItemTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'invoice_id' => $this->resource->invoice_id,
            'user_id' => $this->resource->user_id,
            'name' => $this->getItemName(),
            'quantity' => $this->resource->quantity,
            'quantity_formatted' => number_format($this->resource->quantity, 2),
            'price' => $this->resource->price,
            'description' => $this->resource->description,
            'created_at' => $this->resource->created_at,
            'subtotal' => $this->resource->subtotal(),
            'is_delivery_ticket' => same_morph_alias(
                DeliveryTicket::class,
                $this->resource->item_type,
            ),
            'is_delivery_ticket_materials' => same_morph_alias(
                DeliveryTicketMaterial::class,
                $this->resource->item_type,
            ),
            'is_order_product' => same_morph_alias(
                OrderProduct::class,
                $this->resource->item_type,
            ),
            'is_work_log' => same_morph_alias(
                WorkLog::class,
                $this->resource->item_type,
            ),
            'send_date' => $this->resource->send_date,
        ];
    }

    public function withFormattedValues(string $currency): self
    {
        return $this->state(function ($attrs) use ($currency) {
            return [
                'price_formatted' => number_to_money($this->resource->price, $currency),
                'subtotal_formatted' => number_to_money(data_get($attrs, 'subtotal'), $currency),
            ];
        });
    }

    protected function getItemName(): ?string
    {
        if (same_morph_alias(WorkLog::class, $this->resource->item_type)) {
            return $this->resource->name;
        }

        if (same_morph_alias(InvoiceManualItem::class, $this->resource->item_type)) {
            return $this->resource->name;
        }

        /**
         * transfer this all to modules/invoicify
         */
        if (same_morph_alias(ModelsInvoiceManualItem::class, $this->resource->item_type)) {
            return $this->resource->name;
        }

        return $this->resource->item ? collect($this->resource->item->details)->get('name') : '';
    }

    public function withItemType()
    {
        return $this->state(fn () => [
            'item_type' => match ($this->resource->item_type) {
                'delivery_tickets' => [
                    'label' => trans('headings.delivery_tickets'),
                    'color' => '#009688',
                    'icon' => 'local_shipping',
                ],
                'order_product' => [
                    'label' => trans('order.labels.used-products'),
                    'color' => '#3F51B5',
                    'icon' => 'precision_manufacturing',
                ],
                'delivery_ticket_materials' => [
                    'label' => trans('delivery_tickets.message.materials.on-delivery-ticket', [
                        'ticket' => data_get(
                            $this->resource->details,
                            'delivery_ticket.external_id',
                        ),
                    ]),
                    'color' => '#00BCD4',
                    'icon' => 'shopping_cart_checkout',
                ],
                'work_logs' => [
                    'label' => trans('order.work_log.order-work-logs'),
                    'color' => '#2196F3',
                    'icon' => 'history',
                ],
                'manual_items' => [
                    'label' => trans('invoices.labels.manual-item'),
                    'color' => '#fe4e89',
                    'icon' => 'draw',
                ],
            },
        ]);
    }

    public function withDetails()
    {
        return $this->state(fn () => [
            'details' => $this->resource->details,
        ]);
    }

    public function withItemDetails()
    {
        $details = collect(
            data_get(
                $this->resource->details,
                'details',
                $this->resource->details,
            ),
        );

        $media = $details->get('media');

        $details->put(
            'media_url',
            $media['url'] ?? data_get($details->get('user'), 'photo') ?? null,
        );

        $details->put(
            'media_thumbnail_url',
            $media['media_thumbnail_url'] ?? data_get($details->get('user'), 'photo') ?? null,
        );

        if (same_morph_alias(WorkLog::class, $this->resource->item_type)) {
            $item = $this->resource->item->loadMissing(['company', 'user']);
            $contractor = $item->company;

            $companyService = CompanyService::make($contractor);

            $details->put('type', trans('labels.employees'));
            $details->put('employee_id', $companyService->getEmployeeUuid($item->user));
            $details->put('vendor_id', null);
            $details->put(
                'internal_id',
                implode(
                    '-',
                    [
                        $companyService->getCompanyInitial(),
                        $companyService->getEmployeeCode($item->user),
                    ],
                ),
            );
        }

        return $this->state(fn () => [
            'details' => $details,
            'parent_item_type' => $this->resource->item_type,
            'parent_item_id' => $this->resource->item_id,
        ]);
    }

    public function withActions(?User $auth = null)
    {
        $this->resource->load('invoice');
        $itemService = InvoiceItemService::make($this->resource);

        return $this->state(fn () => [
            'actions' => [
                'editable' => $itemService->userCanEdit(),
                'editable_price' => $itemService->userCanEditPrice(),
                'editable_quantity' => $itemService->userCanEditQuantity(),
                'deletable' => $itemService->userCanDelete(),
            ],
        ]);
    }
}
