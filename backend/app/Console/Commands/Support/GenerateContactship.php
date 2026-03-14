<?php

namespace App\Console\Commands\Support;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateContactship extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:generate-contactship';

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
        $contacts = $this->getOverallContacts();

        $this->info("Generating contactships for ({$contacts->count()}) contacts...");

        $progressBar = $this->output->createProgressBar($contacts->count());

        $contacts->each(function ($contact) {});

        return 0;
    }

    protected function getOverallContacts()
    {
        $overallCompanyContacts = Company::all()
            ->map(function ($company) {
                return $company->contacts;
            })
            ->flatten();

        $overallUserContacts = User::all()
            ->map(function ($user) {
                return $user->contacts;
            })
            ->flatten();

        return $overallUserContacts->merge($overallCompanyContacts);
    }
}
