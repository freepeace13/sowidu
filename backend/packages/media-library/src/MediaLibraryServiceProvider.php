<?php

namespace Packages\MediaLibrary;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Packages\MediaLibrary\Conversions\Commands\RegenerateCommand as RegenerateConversionsCommand;
use Packages\MediaLibrary\Conversions\ImageGenerators\Image;
use Packages\MediaLibrary\Conversions\ImageGenerators\Pdf;
use Packages\MediaLibrary\Conversions\ImageGenerators\Video;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred;
use Packages\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\TinyPlaceholderGenerator;
use Packages\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator;
use Packages\MediaLibrary\ResponsiveImages\WidthCalculator\WidthCalculator;

class MediaLibraryServiceProvider extends ServiceProvider
{
    public function register()
    {
        Relation::morphMap([
            'media' => Media::class,
        ]);

        $this->commands([
            RegenerateConversionsCommand::class,
        ]);

        $this->app->bind(Filesystem::class, Filesystem::class);
        $this->app->bind(WidthCalculator::class, FileSizeOptimizedWidthCalculator::class);
        $this->app->bind(TinyPlaceholderGenerator::class, Blurred::class);

        $this->ensureRequirementsAreInstalled();
        $this->ensureFilesystemsConfigured();
    }

    public function boot()
    {
        //
    }

    private function ensureRequirementsAreInstalled()
    {
        if (!(new Pdf)->requirementsAreInstalled() ||
            !(new Video)->requirementsAreInstalled() ||
            !(new Image)->requirementsAreInstalled()
        ) {
            throw new InvalidArgumentException('Media library requirements not installed.');
        }
    }

    private function ensureFilesystemsConfigured()
    {
        $diskName = config('media-library.disk_name');

        if (!array_key_exists($diskName, config('filesystems.disks'))) {
            config([
                "filesystems.disks.{$diskName}" => [
                    'driver' => 'local',
                    'root' => env('MEDIA_STORAGE', storage_path('app')) . '/media-library',
                ],
            ]);
        }
    }
}
