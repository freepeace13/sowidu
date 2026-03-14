<?php

namespace App\Console\Commands\Media;

use App\Actions\Media\ConvertHeicToJpg;
use Illuminate\Console\Command;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ConvertHeicImagesToJpg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:heictojpg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will convert existing .heic media images to jpg.';

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
    public function handle(ConvertHeicToJpg $converter)
    {
        $heicImages = Media::where('mime_type', 'image/heic')->get();

        $this->withProgressBar($heicImages, function ($media) use ($converter) {
            $converter->convert($media);
        });

        return 0;
    }
}
