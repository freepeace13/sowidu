<?php

namespace App\Console\Commands\Utils;

use App\Models\DeliveryTicketMaterial;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Console\Command;

class SeedDeliveryTicketToInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-delivery-ticket-to-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will seed delivery_ticket_invoice pivot table.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Seeding delivery_ticket to invoice...');

        // Get all invoice_items
        $this->withProgressBar(
            InvoiceItem::query()
                ->where('item_type', (new DeliveryTicketMaterial)->getMorphClass())
                ->with(['invoice', 'item'])
                ->cursor(),
            function (InvoiceItem $invoiceItem) {
                $invoice = $invoiceItem->invoice;
                $deliveryTicket = $invoiceItem->item->deliveryTicket()
                    ->first();

                if (
                    $invoice->deliveryTickets()
                        ->where('id', $deliveryTicket->getKey())
                        ->exists()
                ) {
                    return;
                }

                $invoice->deliveryTickets()
                    ->attach($deliveryTicket);
            },
        );

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
