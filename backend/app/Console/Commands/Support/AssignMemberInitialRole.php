<?php

namespace App\Console\Commands\Support;

use App\Models\Employee;
use App\Repositories\RoleRepository;
use Illuminate\Console\Command;

class AssignMemberInitialRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:assign-member-initial-role';

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
        $this->info('Assigning member initial role...');

        $members = Employee::whereNotNull('role')->get();

        foreach ($members as $member) {
            if ($member->hasRole($member->role)) {
                continue;
            }

            $member->assignRole(RoleRepository::createFor($member->employer)
                ->firstOrCreate($member->role));
        }

        $this->info("Done \n");

        return 0;
    }
}
