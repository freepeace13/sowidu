<?php

namespace App\Console\Commands\Support;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class DeleteUnrecognizeNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:delete-unrecognize-notifications';

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
        $this->info('Delete uncognize notifications...');

        $unrecognizeNotifications = [
            \App\Notifications\Invitation\InvitationAccepted::class,
            \App\Notifications\Invitation\InvitationSent::class,
        ];

        DatabaseNotification::whereIn('type', $unrecognizeNotifications)->delete();

        $this->info("Notifications deleted! \n");

        return 0;
    }
}
