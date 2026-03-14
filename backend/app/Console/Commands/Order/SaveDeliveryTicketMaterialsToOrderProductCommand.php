<?php

namespace App\Console\Commands\Order;

use App\Models\DeliveryTicket;
use App\Services\Order\OrderItemService;
use Illuminate\Console\Command;

class SaveDeliveryTicketMaterialsToOrderProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:ticket-materials-to-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch all ticket materials and save it on the order product.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DeliveryTicket::query()
            ->has('materials')
            ->with(['order', 'user', 'company'])
            ->cursor()
            ->each(function (DeliveryTicket $deliveryTicket) {
                // Check if ticket is associated with order
                if (blank($deliveryTicket->order_id)) {
                    return;
                }

                $order = $deliveryTicket->order;
                $user = $deliveryTicket->user;
                $company = $deliveryTicket->company;

                $this->info("Saving materials on order: {$order->id}");

                $materials = $deliveryTicket->materials()->pluck('id')->toArray();
                OrderItemService::make($order, $user, $company)
                    ->addProducts($materials, $deliveryTicket);
            });

        return Command::SUCCESS;
    }
}
