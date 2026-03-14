<?php

namespace Modules\Offer\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Modules\Offer\Contracts\External\CatalogServiceContract;
use Modules\Offer\Models\OfferItem;

class SeedUnitNamesOnOffersCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:seed-unit-names-on-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will seed the unit names on offers.';

    public function __construct(
        protected CatalogServiceContract $catalogService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Seeding unit names on offers...');

        $totalItems = OfferItem::count();

        if ($totalItems === 0) {
            $this->info('No offer items found to process.');

            return Command::SUCCESS;
        }

        // Create progress bar
        $progressBar = $this->output->createProgressBar($totalItems);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        $processed = 0;

        OfferItem::chunk(10, function ($offerItems) use ($progressBar, &$processed) {
            $offerItems->each(function ($offerItem) {
                $details = $offerItem->details;

                if ($details->has('unit_name')) {
                    return;
                }

                $unitId = $details->get('unit');
                $unit = $this->catalogService->findUnit($unitId);

                if (!$unit) {
                    return;
                }

                $unitName = $this->catalogService->getUnitName($unit);

                $details->put('unit_name', $unitName);
                $details->put('unit_id', $unit->id);
                $details->put('unit', $unitName);

                $offerItem->details = $details;
                $offerItem->save();

                $this->info("Updated offer item {$offerItem->id} with unit name {$unitName}");
            });

            $processed += $offerItems->count();
            $progressBar->advance($offerItems->count());
        });

        $progressBar->finish();
        $this->newLine();
        $this->info("Migration completed! Processed {$processed} offer items.");

        return Command::SUCCESS;
    }
}
