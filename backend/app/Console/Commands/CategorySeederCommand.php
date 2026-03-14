<?php

namespace App\Console\Commands;

use App\Actions\Category\SaveDefaultCategoriesToAccount;
use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;

class CategorySeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will save categories for all organizations and users.';

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
        if (!$this->confirm('All categories will removed! This action cannot be undone. Do you wish to proceed?')) {
            return 0;
        }

        Category::truncate();

        $this->withProgressBar(Company::all(), function (Company $company) {
            $this->line('Saving categories for: ' . $company->name);
            (new SaveDefaultCategoriesToAccount)->save($company);
        });

        $this->info('All default settings for company has been saved.');

        $this->info('Start saving categories for Users.');
        $this->withProgressBar(User::all(), function (User $user) {
            $this->line('Saving categories for: ' . $user->email);
            (new SaveDefaultCategoriesToAccount)->save($user);
        });

        return 0;
    }
}
