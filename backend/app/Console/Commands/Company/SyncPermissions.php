<?php

namespace App\Console\Commands\Company;

use App\Models\Company;
use App\Models\Permission;
use Illuminate\Console\Command;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all permissions to the existing company owners.';

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
     * @return mixed
     */
    public function handle()
    {
        foreach (Company::all() as $company) {
            $company->founder->syncPermissions(
                Permission::whereGuard('commercial')->get(),
            );
        }

        return 0;
    }
}
