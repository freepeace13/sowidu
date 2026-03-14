<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MediaStorageLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:storage-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create symbolic link of media storage path';

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
     * @return mixed
     */
    public function handle()
    {
        $target = config('filesystems.disks.media.root');
        $destination = storage_path('app/public/media');

        if (file_exists($destination)) {
            return $this->error('The media storage directory already exists.');
        }

        $this->laravel->make('files')->link($target, $destination);

        $this->info('The media storage directory has been linked.');

        return 0;
    }
}
