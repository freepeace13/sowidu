<?php

namespace App\Console\Commands\Company;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Console\Command;

class SaveMediaSettingsOnCategorySettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:synch-media-settings-on-category-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will add company media settings on category settings for auto share media files to roles.';

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
        // Fetch all companies
        Company::all()
            ->each(function (Company $company) {
                // Iterate on each company

                // Fetch all roles enabled on `media_settings`
                $mediaSettingsAutoSharedRoles = collect($company->settings()
                    ->media()
                    ->getRolesForAutoSharing())
                    ->map(fn ($role) => $company->roles()->where('name', $role)->value('name'))
                    ->push(Company::FOUNDER_ROLE_NAME)
                    ->filter()
                    ->unique()
                    ->toArray();

                // Fix role name in small case and duplicates
                $company
                    ->settings()
                    ->media()
                    ->update('auto_share_to_roles', $mediaSettingsAutoSharedRoles);

                // Save all roles on category settings
                $company->categories()
                    ->with(['ownerable'])
                    ->get()
                    ->each(function (Category $category) use ($mediaSettingsAutoSharedRoles, $company) {
                        $categoryAutoShare = $category
                            ->settings()
                            ->autoShare();

                        // Fetch old settings and merge
                        $oldSettings = $categoryAutoShare->all()
                            ->map(fn ($role) => $company->roles()->where('name', $role)->value('name'))
                            ->push(Company::FOUNDER_ROLE_NAME)
                            ->filter()
                            ->toArray();

                        if (blank($oldSettings)) {
                            return;
                        }

                        $roles = collect(
                            array_iunique([...$oldSettings, ...$mediaSettingsAutoSharedRoles]),
                        )->filter();

                        if ($roles->isEmpty()) {
                            return;
                        }

                        $categoryAutoShare->update($roles->toArray());
                    });

                $this->info("Company {$company->name} done.");
            });

        $this->info('Done.');

        return 0;
    }
}
