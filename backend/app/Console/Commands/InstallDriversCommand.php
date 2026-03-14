<?php

namespace App\Console\Commands;

use App\Actions\SynchronizeClient;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;

class InstallDriversCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:install-drivers';

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
        $this->installClients();

        return 0;
    }

    protected function installClients()
    {
        Employee::all()->each(function ($entry) {
            (new SynchronizeClient)->handle($entry);
        });

        User::all()->each(function ($entry) {
            (new SynchronizeClient)->handle($entry);
        });
    }
}
