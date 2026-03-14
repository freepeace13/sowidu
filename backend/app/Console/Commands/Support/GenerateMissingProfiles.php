<?php

namespace App\Console\Commands\Support;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateMissingProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:generate-missing-profiles';

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
        $this->info('Generating missing profiles...');

        $unregisteredUsers = User::doesntHave('profile')->get();
        $unregisteredEmployees = Employee::doesntHave('profile')->get();
        $unregisteredCompanies = Company::doesntHave('profile')->get();

        $unregisteredProfiles = $unregisteredUsers
            ->merge($unregisteredEmployees)
            ->merge($unregisteredCompanies);

        $progressBar = $this->output->createProgressBar(
            $unregisteredProfiles->count(),
        );

        $unregisteredProfiles->each(function ($model) use ($progressBar) {
            $model->createProfile();

            $progressBar->advance();
        });

        $progressBar->finish();

        return 0;
    }
}
