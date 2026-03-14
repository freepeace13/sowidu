<?php

namespace App\Console\Commands\Support;

use Illuminate\Console\Command;

class AutoFixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:auto-fix';

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
        $this->call('support:generate-company-usernames');
        $this->call('support:generate-missing-profiles');

        return 0;
    }
}
