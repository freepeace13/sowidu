<?php

namespace Modules\Offer\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Collection;
use Modules\Offer\Contracts\External\OrderServiceContract;
use Modules\Offer\Models\Offer;

class MigrateOrderRelationToOfferCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:migrate-order-relation-to-offer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will move the offer_id on orders table to offers table. This command will also fix order authors which offer was accepted by the offer recipient.';

    public function __construct(
        protected OrderServiceContract $orderService,
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
        // NOTE! This should be run on `BETA` only!
        if (!$this->confirmToProceed('This will move the offer_id on orders table to offers table. Are you sure you want to continue?')) {
            return Command::FAILURE;
        }

        $this->info('Migrating offer_id on orders table to offers table...');

        // Get the Order model class from the contract
        $orderClass = $this->orderService->getModelClass();

        $orderClass::whereNotNull('offer_id')
            ->latest('updated_at')
            ->chunk(10, function (Collection $orders) {
                $orders->each(function ($order) {
                    $offerId = $order->offer_id;

                    if (blank($offerId)) {
                        return;
                    }

                    $this->comment("Migrating offer_id {$offerId} on order {$order->id}...");

                    $offer = Offer::find($offerId);

                    if (blank($offer)) {
                        return;
                    }

                    $offer->update(['order_id' => $order->id]);

                    // Verify the author of the ORDER is an employee of the company
                    $orderAuthor = $order->userAuthor()
                        ->first();
                    $offerAuthor = $offer->author()
                        ->first();

                    if ($offerAuthor->isNot($orderAuthor)) {
                        $this->warn('Updating the order author to the offer author...');
                        // Update the order author to the offer author
                        $order->update(['user_id' => $offerAuthor->id]);

                        $products = $order->products()
                            ->where('user_id', '!=', $offerAuthor->id)
                            ->get();

                        if ($products->isEmpty()) {
                            return;
                        }

                        $this->warn('Updating the order products author to the offer author...');
                        $products->each->update(['user_id' => $offerAuthor->id]);
                    }

                    // $order->update(['offer_id' => null]);
                });
            });

        $this->info('Migration completed!');

        return Command::SUCCESS;
    }
}
