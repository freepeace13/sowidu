<?php

namespace Modules\Offer\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Modules\Offer\Models\Offer;
use Modules\Offer\OfferService;

class AttachDefaultTaxToOffersCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:attach-default-tax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will attach default tax to offers.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirmToProceed('This will attach default tax to offers. Are you sure you want to continue?')) {
            return Command::FAILURE;
        }

        $this->info('Attaching default tax to offers...');

        Offer::chunk(10, function (\Illuminate\Support\Collection $offers) {
            $offers->each(function (Offer $offer) {
                $this->info("Attaching default tax to offer {$offer->id}...");
                OfferService::make($offer)->attachDefaultTaxes();
            });
        });

        $this->info('All offers have been processed.');

        return Command::SUCCESS;
    }
}
