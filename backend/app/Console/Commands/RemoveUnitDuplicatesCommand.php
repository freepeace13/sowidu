<?php

namespace App\Console\Commands;

use App\Models\CatalogItem;
use App\Models\CatalogItemUnit;
use Illuminate\Console\Command;

class RemoveUnitDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove_unit_duplicates_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicates in catalog_item_units table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $withDuplicates = CatalogItemUnit::selectRaw('name, COUNT(*) as duplicates')
            ->groupBy('name')
            ->having('duplicates', '>', 1)
            ->get();

        $this->withProgressBar($withDuplicates, function ($duplicate) {
            $unit = CatalogItemUnit::where('name', $duplicate->name)->oldest()->first();
            CatalogItem::whereRelation('type', 'name', '=', $duplicate->name)
                ->update(['unit' => $unit->id]);

            CatalogItemUnit::where('name', '=', $unit->name)
                ->where('id', '!=', $unit->id)
                ->delete();
            $noUnitName = CatalogItem::whereNull('unit_name')->get();

            $this->withProgressBar($noUnitName, function ($item) use ($unit) {
                $item->update([
                    'unit' => $unit->id,
                ]);
            });
        });

        return Command::SUCCESS;
    }
}
