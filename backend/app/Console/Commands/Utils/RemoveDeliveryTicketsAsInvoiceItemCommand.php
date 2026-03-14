<?php

namespace App\Console\Commands\Utils;

use App\Models\DeliveryTicket;
use App\Models\InvoiceItem;
use Illuminate\Console\Command;

class RemoveDeliveryTicketsAsInvoiceItemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-delivery-tickets-as-invoice-item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove delivery tickets as an invoice item.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Removing delivery tickets as invoice item...');

        InvoiceItem::query()
            ->where('item_type', get_morph_alias(DeliveryTicket::class))
            ->get()
            ->each(function (InvoiceItem $invoiceItem) {
                $invoiceItem->delete();
            });

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
