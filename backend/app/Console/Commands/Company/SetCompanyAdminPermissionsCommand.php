<?php

namespace App\Console\Commands\Company;

use App\Actions\Organization\AssignFounderPermissions;
use App\Models\Company;
use App\Repositories\RoleRepository;
use Illuminate\Console\Command;

class SetCompanyAdminPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:set-founder-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will set all permissions for admin or company founder to true.';

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
        Company::query()
            ->with('user')
            ->get()
            ->each(function ($company) {
                $employer = $company->founder;

                if (!$employer) {
                    return;
                }

                $this->info('Setting permissions for ' . $company->name . ' company admin, founder name: ' . $employer->user->full_name . '...');

                if ($employer->roles()->doesntExist()) {
                    $roleRepository = RoleRepository::createFor($company);
                    // Employer roles has not been set because of the seeder
                    $employer->assignRole(
                        $roleRepository->firstOrCreate(Company::FOUNDER_ROLE_NAME),
                    );
                }

                // Rename Role name to `Founder`
                $employer->roles()->first()->update([
                    'name' => Company::FOUNDER_ROLE_NAME,
                ]);

                $employer->update(['role' => Company::FOUNDER_ROLE_NAME]);

                (new AssignFounderPermissions)->execute($company);
            });

        return 0;
    }
}
