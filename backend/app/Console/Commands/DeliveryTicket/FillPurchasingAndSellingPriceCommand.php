<?php

namespace App\Console\Commands\DeliveryTicket;

use App\Models\DeliveryTicketMaterial;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class FillPurchasingAndSellingPriceCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery-ticket:fill-purchasing-and-selling-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill purchasing and selling price for delivery ticket materials';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return Command::SUCCESS;
        }

        $this->info('Updating materials...fill in purchasing and selling prices...');

        DeliveryTicketMaterial::select(['id', 'details'])
            ->with('deliveryTicket')
            ->chunk(50, function ($materials) {
                $materials->each(function (DeliveryTicketMaterial $material) {
                    $purchasingPrice = $material->details->get('purchasing_price');
                    $sellingPrice = $material->details->get('selling_price');

                    $material->update([
                        'purchasing_price' => $purchasingPrice,
                        'selling_price' => $sellingPrice,
                    ]);

                    $this->info("Updated material {$material->id}");
                });
            });

        $this->info('All materials have been updated successfully.');

        return Command::SUCCESS;
    }
}
