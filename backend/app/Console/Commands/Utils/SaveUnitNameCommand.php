<?php

namespace App\Console\Commands\Utils;

use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use App\Models\DeliveryTicketMaterial;
use App\Models\InvoiceItem;
use App\Models\OrderProduct;
use Illuminate\Console\Command;

class SaveUnitNameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-unit-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will save unit name on invoice items, delivery ticket materials and catalog items.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Fetch all unit names
        $unitNames = CatalogItemUnit::all()->mapWithKeys(function ($item) {
            return [$item->id => $item->name];
        })
            ->toArray();

        // Get all catalog items
        $this->info('Getting all catalog items and saving unit name.');
        // Create a new progress bar instance
        $bar = $this->output->createProgressBar(CatalogItem::count());
        CatalogItem::chunk(50, function ($collection) use ($unitNames, $bar) {
            $collection->each(function ($item) use ($unitNames, $bar) {
                $unitName = data_get($unitNames, $item->unit);

                $item->update([
                    'unit_name' => $unitName,
                ]);

                // Advance the progress bar by one unit
                $bar->advance();
            });
        });
        $bar->finish();
        $this->info('All catalog items have been processed.');

        // Save delivery ticket materials unit name
        $this->info('Getting all delivery ticket materials and saving unit name.');
        $bar = $this->output->createProgressBar(DeliveryTicketMaterial::count());
        DeliveryTicketMaterial::chunk(50, function ($collection) use ($unitNames, $bar) {
            $collection->each(function (DeliveryTicketMaterial $item) use ($unitNames, $bar) {
                $details = $item->details;

                $unitId = $details->get('unit');
                $unitName = data_get($unitNames, $unitId);

                $item->update([
                    'details' => $details->put('unit_name', $unitName),
                ]);

                $bar->advance(); // Advance the progress bar by one unit
            });
        });
        $bar->finish();
        $this->info('All delivery ticket materials have been processed.');

        // Update invoice items to save unit_name on details
        $this->info('Getting all invoice items and saving unit name.');
        $bar = $this->output->createProgressBar(InvoiceItem::count());
        InvoiceItem::chunk(50, function ($collection) use ($unitNames, $bar) {
            $collection->each(function (InvoiceItem $item) use ($unitNames, $bar) {
                if ($item->isWorkLog()) {
                    $item->update([
                        'details' => $item->details->forget('details'),
                    ]);

                    return;
                }

                $details = $item->details;

                if ($item->isDeliveryTicketMaterial()) {
                    $details->forget('details');
                    $item->update([
                        'details' => $details->put('unit_name', data_get($unitNames, $details->get('unit'))),
                    ]);

                    return;
                }

                $itemDetails = collect($details->get('details'));
                $unitId = $itemDetails->get('unit');

                if (blank($unitId)) {
                    return;
                }

                $itemDetails->put('unit_name', data_get($unitNames, $unitId));

                $item->update([
                    'details' => $details->put('details', $itemDetails->toArray()),
                ]);

                $bar->advance(); // Advance the progress bar by one unit
            });
        });
        $bar->finish();
        $this->info('All invoice items have been processed.');

        // Update order products to save unit_name on details
        $this->info('Getting all order products and saving unit name.');
        $bar = $this->output->createProgressBar(OrderProduct::count());
        OrderProduct::chunk(50, function ($collection) use ($unitNames, $bar) {
            $collection->each(function (OrderProduct $item) use ($unitNames, $bar) {
                $details = collect($item->details);

                $unitId = $details->get('unit');

                if (blank($unitId)) {
                    return;
                }

                $unitName = data_get($unitNames, $unitId);

                $item->update([
                    'details' => $details->put('unit_name', $unitName),
                ]);

                $bar->advance(); // Advance the progress bar by one unit
            });
        });
        $bar->finish();
        $this->info('All order products have been processed.');

        return Command::SUCCESS;
    }
}
