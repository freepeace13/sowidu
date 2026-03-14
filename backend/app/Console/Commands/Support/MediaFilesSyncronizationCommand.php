<?php

namespace App\Console\Commands\Support;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Media;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFilesSyncronizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:sync-media-files';

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
        Media::all()
            ->filter(function ($media) {
                return Storage::disk('media-old')->exists($media->filename);
            })
            ->each(function ($media) {
                $originalPath = Storage::disk('media-old')->path($media->filename);

                $fileName = pathinfo($originalPath, PATHINFO_FILENAME);
                $extension = $this->determineExtension($media);

                $newFileName = "{$fileName}.{$extension}";

                $this->determineNewOwner($media)
                    ->addMedia($originalPath)
                    ->setFileName($newFileName)
                    ->withCustomProperties([
                        'original_media_id' => $media->id,
                    ])
                    ->save();
            });

        return 0;
    }

    protected function determineNewOwner($media)
    {
        $owner = $media->owner;

        if ($owner instanceof Company) {
            $owner = Employee::query()
                ->where('user_id', $owner->user_id)
                ->where('company_id', $owner->getKey())
                ->first();
        }

        return $owner;
    }

    protected function determineExtension($media)
    {
        $path = Storage::disk('media-old')->path($media->filename);

        $extension = str_replace('_', '', pathinfo($path, PATHINFO_EXTENSION));

        if (!$extension) {
            $extension = Str::afterLast($media->mimetype, '/');
        }

        $extension = ['jpeg' => 'jpg'][$extension] ?? $extension;

        if (in_array($extension, ['mp4', 'jpg', 'png'])) {
            return $extension;
        }

        throw new Exception('Unknown file extension ' . $extension);
    }
}
