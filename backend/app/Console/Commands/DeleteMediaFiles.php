<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sowidu\Features\Media\Models\MediaFile;

class DeleteMediaFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:delete {type}';

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
        $type = $this->argument('type');

        $supportedMimeTypes = config('media-feature.mime_types.' . $type, []);

        $videos = MediaFile::withTrashed()
            ->whereIn('mime_type', $supportedMimeTypes)
            ->get();

        $progressBar = $this->output->createProgressBar($videos->count());

        $videos->each(function ($video) use ($progressBar) {
            $video->forceDelete();

            $progressBar->advance();
        });

        $progressBar->finish();

        return 0;
    }
}
