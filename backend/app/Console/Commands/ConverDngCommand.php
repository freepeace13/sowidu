<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class ConverDngCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:conver-dng-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $dngFiles = Media::where('mime_type', 'image/x-adobe-dng')
            ->orWhere('file_name', 'like', '%.dng')
            ->get();
        dump($dngFiles->count());
        $this->withProgressBar($dngFiles, function ($dngFiles) {
            dump($dngFiles);
        });

        return Command::SUCCESS;
    }
}
