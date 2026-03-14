<?php

namespace App\Console;

use App\Console\Commands\Chat\WatchUnSeenChatMessagesCommand;
use App\Console\Commands\Invoice\CleanOldInvoiceArchivesCommand;
use App\Console\Commands\Invoice\PopulateInvoicePreviewLayoutCommand;
use App\Console\Commands\Utils\SyncAddressbooksSourceCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CountrySync::class,
        Commands\Company\SyncPermissions::class,
        Commands\JSONTableExport::class,
        Commands\JSONTableImport::class,
        Commands\Auth\SendActivationUrl::class,
        Commands\MediaStorageLinkCommand::class,
        Commands\Support\AutoFixCommand::class,
        Commands\Support\GenerateCompanyUsernames::class,
        Commands\Support\GenerateMissingProfiles::class,
        Commands\Support\MediaFilesSyncronizationCommand::class,
        Commands\Support\SynchronizeExistingRoles::class,
        Commands\Support\AssignMemberInitialRole::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('activitylog:clean --force')->daily();
        $schedule->command('apilog:clear')->daily();
        $schedule->command('queue:prune-failed --hours=48')->daily();
        $schedule->command('queue:prune-batches')->daily();
        $schedule->command('queue:prune-batches --hours=48 --cancelled=48 --unfinished=24')->daily();

        $schedule->command(WatchUnSeenChatMessagesCommand::class)->everyMinute();
        $schedule->command(CleanOldInvoiceArchivesCommand::class)->daily();
        $schedule->command(SyncAddressbooksSourceCommand::class)->twiceDaily();

        if ($this->app->environment('staging') || config('telescope.enabled')) {
            $schedule->command('telescope:prune --hours=24')->daily();
        }

        // $schedule->command(PopulateInvoicePreviewLayoutCommand::class)
        //     ->twiceDailyAt(1, 3);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function bootstrappers()
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
