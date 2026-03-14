<?php

namespace App\Console\Commands\Support;

use App\Models\Company;
use App\Repositories\RoleRepository;
use Illuminate\Console\Command;

class SynchronizeExistingRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:sync-existing-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->info('Synchronizing existing roles into departments...');

        Company::each(function ($company) {
            $roles = $company->employees
                ->map
                ->role
                ->unique()
                ->filter()
                ->values()
                ->all();

            $repository = RoleRepository::createFor($company);

            foreach ($roles as $role) {
                $repository->firstOrCreate($role);
            }
        });

        $this->info("Done \n");

        return 0;
    }
}
