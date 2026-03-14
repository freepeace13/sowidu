<?php

namespace App\Console\Commands\Support;

use App\Models\Company;
use Illuminate\Console\Command;
use TaylorNetwork\UsernameGenerator\Generator;

class GenerateCompanyUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:generate-company-usernames';

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
        $this->info('Generating company usernames...');

        $companies = Company::all();

        $progressBar = $this->output->createProgressBar($companies->count());

        $companies->each(function ($company) use ($progressBar) {
            if (blank($company->username)) {
                $company->username = (new Generator)->generateFor($company);
            }

            $company->save();
            $progressBar->advance();
        });

        $progressBar->finish();

        return 0;
    }
}
