<?php

namespace App\Console\Commands\Media;

use App\Actions\Category\RemoveCategoryTagOnAllMediaOwned;
use App\Models\Category;
use App\Models\Company;
use App\Services\MediaFileService;
use Illuminate\Console\Command;

class RemoveCategoryOnMediaIfCategoryIsDeletedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:cleanse-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will remove category when category is deleted!';

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
        Company::query()
            ->select(['id', 'name'])
            ->get()
            ->each(function (Company $company) {
                $this->info('Checking ' . $company->name . ' company categories...');

                $categories = MediaFileService::makeForCompany($company)
                    ->select(['id', 'category', 'model_id', 'model_type'])
                    ->whereNotNull('category')
                    ->groupBy('category')
                    ->get()
                    ->pluck('category');

                $categories->each(function ($category) use ($company) {
                    // Find on `Category` - if not found remove category on `MediaFile`
                    $this->line("Checking $category category...");

                    if (Category::query()
                        ->whereMorphRelation('ownerable', Company::class, 'id', $company->id)
                        ->whereLike('name', $category)
                        ->first()) {
                        return;
                    }

                    // Category has been deleted...
                    $this->warn(
                        "Category {$category} has been deleted...Removing category on medias...",
                    );

                    (new RemoveCategoryTagOnAllMediaOwned)->remove($company, $category);
                });

                $this->newLine();
            });

        return 0;
    }
}
