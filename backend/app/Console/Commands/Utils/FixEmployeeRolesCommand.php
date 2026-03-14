<?php

namespace App\Console\Commands\Utils;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Console\Command;

class FixEmployeeRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-employee-roles';

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
        Company::with(['user'])->get()
            ->each(function (Company $company) {
                $userId = $company->user_id;

                $this->info('Checking..' . $company->name . '.');
                $company->employees()
                    ->where('user_id', $userId)
                    ->with(['user'])
                    ->get()
                    ->each(fn ($employee) => $this->info('Founder: ' . $employee->user->email));

                $company->employees()
                    ->with(['user'])
                    ->get()
                    ->each(function (Employee $employee) use ($company) {
                        $roles = $employee->roles()->pluck('name')->toArray();
                        if (in_array(Company::FOUNDER_ROLE_NAME, $roles) && $company->user->isNot($employee->user)) {
                            $this->warn('This employee has a Founder role! ' . $employee->user->email);

                            $this->warn('Removing role...');

                            $employee->removeRole(Company::FOUNDER_ROLE_NAME);
                        }
                    });

                $this->newLine();
            });

        $this->info('All done. You can run this command again to find out multiple roles.');

        return 0;
    }
}
