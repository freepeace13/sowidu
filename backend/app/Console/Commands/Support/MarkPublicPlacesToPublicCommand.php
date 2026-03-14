<?php

namespace App\Console\Commands\Support;

use App\Models\Place;
use Illuminate\Console\Command;

/**
 * In used `v2`
 */
class MarkPublicPlacesToPublicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:mark-public-places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will mark public places or places used on address records as public.';

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
        Place::where('label', 'like', Place::PUBLIC . '%')
            ->update(['is_private' => false]);

        $this->info('All public address/places records are now public.');

        return 0;
    }
}
