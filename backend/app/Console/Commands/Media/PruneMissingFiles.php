<?php

namespace App\Console\Commands\Media;

use Illuminate\Console\Command;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class PruneMissingFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:prune-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all media that original file is missing.';

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
        $media = Media::all();

        $this->withProgressBar($media, function ($media) {
            if (!file_exists($media->getPath())) {
                $media->forceDelete();
            }
        });

        return 0;
    }
}
