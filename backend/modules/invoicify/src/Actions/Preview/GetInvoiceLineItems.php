<?php

namespace Modules\Invoicify\Actions\Preview;

use App\Actions\Traits\AsAction;
use App\Transformers\InvoiceItemTransformer;
use Modules\Invoicify\Models\Invoice;
use Modules\Invoicify\Services\InvoiceService;

class GetInvoiceLineItems
{
    use AsAction;

    public function handle(Invoice $invoice, array $options = []): array
    {
        $service = InvoiceService::run($invoice);
        $toPdfa = data_get($options, 'to_pdfa', false);

        $lineItems = $lineItems = collect(
            $service->groupItems($invoice
                ->items()
                ->with(['item', 'company'])
                ->get()
                ->map(
                    fn ($item) => (new InvoiceItemTransformer($item))
                        ->withItemType()
                        ->withItemDetails()
                        ->withFormattedValues($service->currency())
                        ->resolve(),
                )),
        )->map(function ($item, $index) use ($toPdfa) {
            $unitName = '';

            if ($item['is_work_log']) {
                $unitName = trans('labels.hour');
            } elseif ($item['is_order_product']) {
                $unitName = data_get($item, 'details.unit_name');
            } else {
                $unitName = data_get($item, 'details.unit_name');
            }

            if (blank($unitName)) {
                $unitName = '--';
            }

            $lineItem = [
                'id' => $item['id'],
                'line_item_number' => str($index + 1)->padLeft(3, '0')->toString(),
                'quantity' => $item['quantity_formatted'],
                'unit_name' => $unitName,
                'name' => $item['name'],
                'price' => $item['price'],
                'price_formatted' => $item['price_formatted'],
                'subtotal' => $item['subtotal'],
                'subtotal_formatted' => $item['subtotal_formatted'],
            ];

            if ($toPdfa) {
                $lineItem = array_merge($item, $lineItem);
            }

            return $lineItem;
        });

        return $lineItems->toArray();
    }
}
