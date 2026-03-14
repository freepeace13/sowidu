<?php

namespace App\Console\Commands\Company;

use App\Models\Company;
use Illuminate\Console\Command;

class SaveOrganizationDefaultSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:save-settings {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will save default settings for the organizations.';

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
        if ($this->option('reset') && !$this->confirm('Organization settings will be reset! Do you wish to continue?')) {
            return 0;
        }

        $this->withProgressBar(Company::all(), function (Company $company) {
            $this->line('Saving default settings for company: ' . $company->name);

            if (!$this->option('reset') && filled($company->settings)) {
                return;
            }

            $company->settings()->saveDefaults();
        });

        $this->info('All default settings for company has been saved.');

        return 0;
    }
}
