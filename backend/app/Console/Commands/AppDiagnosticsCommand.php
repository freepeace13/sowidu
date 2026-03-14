<?php

namespace App\Console\Commands;

use App\Enums\Permissions;
use Illuminate\Console\Command;

class AppDiagnosticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:diagnostics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will check, verify and diagnose the application for any issues. Like permissions, etc.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Diagnosing application....');

        $this->checkPermissions();

        return Command::SUCCESS;
    }

    protected function checkPermissions()
    {
        $this->info('Checking Permissions...');
        $groupings = collect(Permissions::groupings())->map(fn ($g) => $g['permissions'])
            ->flatten()
            ->toArray();

        $allPermissions = collect(Permissions::all())
            ->reject(
                fn ($permission) => in_array($permission, Permissions::forPrivateUserOnly()),
            )
            ->toArray();

        $hasError = false;
        $ungrouped = [];

        foreach ($allPermissions as $permission) {
            if (!in_array($permission, $groupings)) {
                $hasError = true;
                $this->warn("Permission {$permission} is not grouped!");
                $ungrouped[] = $permission;
            }
        }

        if ($hasError) {
            $this->error('Some permissions are not grouped! Please go to app/Enums/Permissions.php and group them.');
            $this->table(
                ['Ungrouped Permissions'],
                collect($ungrouped)->map(fn ($p) => [$p])
                    ->toArray(),
            );
            $this->error('Please fix this issue to ensure proper permission management.');
            $this->error('You can run `php artisan app:diagnostics` to check again after fixing.');

            return;
        }

        $this->comment('Permissions are all grouped!');
    }
}
