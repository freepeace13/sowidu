<?php

namespace App\Console\Commands\Support;

use DB;
use Illuminate\Console\Command;

class DeleteNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:delete-notification {criteria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will delete notification based on criteria.';

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
        $criteria = $this->argument('criteria');

        $count = DB::table('notifications')
            ->where('type', $criteria)
            ->whereNull('read_at')
            ->count();

        if (!$this->confirm("Are you sure you want to delete all notification with type: $criteria? There are $count results found. This action cannot be undone.")) {
            return Command::INVALID;
        }

        DB::table('notifications')
            ->where('type', $criteria)
            ->whereNull('read_at')
            ->delete();

        return 0;
    }
}
