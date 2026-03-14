<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Services\CatalogService;

class UpdateCatalogItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-catalog-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update catalog items and save needed values.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CatalogItem::query()
            ->with(['author', 'company'])
            ->chunk(100, function ($items) {
                $items->each(function (CatalogItem $catalogItem) {
                    $user = $catalogItem->author;
                    $company = $catalogItem->company;

                    CatalogService::make($user, $company)
                        ->updateVendorAndInternalIds($catalogItem);

                    $this->info('Catalog item updated.');
                });
            });

        return 0;
    }
}
