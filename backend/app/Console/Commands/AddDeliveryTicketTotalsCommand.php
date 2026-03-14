<?php

namespace App\Console\Commands;

use App\Models\DeliveryTicket;
use Illuminate\Console\Command;

class AddDeliveryTicketTotalsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add_delivery_ticket_totals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add totals to delivery_tickets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deliveryTickets = DeliveryTicket::all();

        $this->withProgressBar($deliveryTickets, function ($deliveryTicket) {
            $deliveryTicket->update([
                'total_purchasing_price' => $deliveryTicket->materials()->selectRaw('SUM(quantity * purchasing_price) as total_purchasing_price')->value('total_purchasing_price'),
                'total_selling_price' => $deliveryTicket->materials()->selectRaw('SUM(quantity * selling_price) as total_selling_price')->value('total_selling_price'),

            ]);
        });

        return Command::SUCCESS;
    }
}
