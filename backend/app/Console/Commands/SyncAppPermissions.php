<?php

namespace App\Console\Commands;

use App\Actions\Organization\AssignFounderPermissions;
use App\Enums\Permissions;
use App\Models\Company;
use App\Models\Permission;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAppPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-permissions
                            {--reset-user-permissions : Reset user permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will get permissions from `config/app.php` and add permissions on Database if not found.';

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
        $this->info('Synching app permissions...');

        if ($this->option('reset-user-permissions')) {
            DB::table('model_permissions')
                ->where('model_type', '=', (new User)->getMorphClass())
                ->delete();
        }

        collect(Permissions::all())
            ->each(
                fn ($permission) => Permission::query()
                    ->updateOrCreate(['name' => $permission]),
            );

        Company::query()
            ->with('user')
            ->get()
            ->each(function ($company) {
                $employer = $company->founder;

                if (!$employer) {
                    return;
                }

                $this->info('Setting permissions for ' . $company->name . ' company admin, founder name: ' . $employer->user->full_name . '...');

                if (
                    $employer->roles()
                        ->doesntExist()
                ) {
                    $roleRepository = RoleRepository::createFor($company);
                    // Employer roles has not been set because of the seeder
                    $employer->assignRole(
                        $roleRepository->firstOrCreate(Company::FOUNDER_ROLE_NAME),
                    );
                }

                (new AssignFounderPermissions)->execute($company);
            });

        // Remove permission if not found on the config file
        $appPermissions = Permissions::all();
        Permission::all()
            ->each(function ($permission) use ($appPermissions) {
                if (!in_array($permission->name, $appPermissions)) {
                    // Delete this Permission
                    $permission->delete();
                }
            });

        $this->syncDefaultPermissionsForPrivateUsers();

        $this->info('All permissions are all set.');

        return 0;
    }

    protected function syncDefaultPermissionsForPrivateUsers()
    {
        $this->newLine();
        $this->info('Syncing default permissions for private users...');

        $privateUserPermissions = Permissions::forPrivateUser();

        $bar = $this->output->createProgressBar(User::query()->count());
        $bar->start();

        User::query()
            ->get(['id', 'first_name', 'last_name'])
            ->each(function ($user) use ($privateUserPermissions, $bar) {
                $this->info('Setting permissions for ' . $user->full_name . '...');
                $user->loadMissing(['permissions', 'roles']);
                $user->givePermissionTo($privateUserPermissions);
                $bar->advance();
            });

        $bar->finish();
        $this->newLine();

        return 0;
    }
}
