<?php

namespace App\Console\Commands\Order;

use App\Models\DeliveryTicketMaterial;
use App\Models\OrderProduct;
use Illuminate\Console\Command;

class TransferDeliveryTicketMaterialsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:transfer-ticket-materials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will transfer delivery_ticket_materials data.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        OrderProduct::query()
            ->whereNotNull('delivery_ticket_id')
            ->cursor()
            ->each(function (OrderProduct $orderProduct) {
                $this->info('Moving order_product to ticket materials...');

                $material = DeliveryTicketMaterial::make(
                    $orderProduct->only(['quantity', 'details']),
                );

                $material->deliveryTicket()->associate($orderProduct->deliveryTicket);
                $material->user()->associate($orderProduct->user);

                $material->save();

                $orderProduct->delete();
            });

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
