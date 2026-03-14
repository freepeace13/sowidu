<?php

namespace App\Listeners\Invoice;

use App\Enums\InvoiceStatus;
use App\Events\Invoice\InvoiceSent;
use App\Models\InvoiceItem;
use App\Modules\Invoice\InvoiceService;
use App\Modules\Invoice\Services\InvoiceSummaryService;
use App\Transformers\InvoiceItemTransformer;
use App\Transformers\InvoiceTransformer;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @todo remove final_data
 * TODO - remove this listener and move of saving invoice summary `saveInvoiceSummarries()` to another listener
 */
class GenerateInvoiceFinalData implements ShouldQueue
{
    public function handle(InvoiceSent $event)
    {
        $invoice = $event->invoice;

        if ($invoice->status == InvoiceStatus::DRAFT) {
            return;
        }

        // Generate invoice final data
        $service = InvoiceService::run($invoice);

        $executionPeriod = $service->getExecutionPeriod();
        $startedAt = data_get($executionPeriod, 'started_at');
        $endedAt = data_get($executionPeriod, 'ended_at');

        $order = $invoice->order()
            ->with(['client', 'deliveryAddress'])
            ->first();

        $invoiceSummary = InvoiceSummaryService::run($invoice);

        // Save invoice total summaries
        $invoiceSummary->saveInvoiceSummarries();

        $currency = $invoiceSummary->currency();
        $subtotal = $invoiceSummary->subtotal();
        $netAmount = $invoiceSummary->netAmount();
        $grandTotal = $invoiceSummary->grandTotal();

        $invoiceFinalData = (new InvoiceTransformer($invoice))
            ->withStatus()
            ->withTaxes(
                $invoiceSummary->taxes()
                    ->get(),
            )
            ->withOrderFullDetails($order)
            ->withOrderClientDetails($order)
            ->withCompanyFullDetails($invoice->company)
            ->withExecutionPeriod($startedAt, $endedAt)
            ->resolve();

        $items = $service->groupItems(
            $invoice
                ->items()
                ->with(['item', 'company'])
                ->get()
                ->map(
                    fn (InvoiceItem $item) => (new InvoiceItemTransformer($item))
                        ->withItemType()
                        ->withItemDetails()
                        ->resolve(),
                ),
        );

        $invoice->update([
            'final_data' => [
                'invoice' => $invoiceFinalData,
                'total_price' => $service->totalAmount(),
                'items' => $items,
                'invoice_summary' => [
                    'subtotal' => $subtotal,
                    'net_amount' => $netAmount,
                    'grand_total' => $grandTotal,
                    'subtotal_formatted' => number_to_money($subtotal, $currency),
                    'net_amount_formatted' => number_to_money($netAmount, $currency),
                    'grand_total_formatted' => number_to_money($grandTotal, $currency),
                ],
            ],
        ]);
    }
}
