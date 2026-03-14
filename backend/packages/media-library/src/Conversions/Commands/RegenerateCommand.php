<?php

namespace Packages\MediaLibrary\Conversions\Commands;

use Illuminate\Console\Command;
use Packages\MediaLibrary\Conversions\Actions\PerformRegenerationAction;
use Packages\MediaLibrary\Conversions\ImageGenerators\Image;
use Packages\MediaLibrary\Conversions\ImageGenerators\ImageGeneratorFactory;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class RegenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media-library:regenerate-conversions
        {--types=* : Regenerate only media types}
        {--with-responsive-images : Regenerate responsive images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate media files conversions';

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
        $this->info('Regenerating media conversion files...');

        $imageGenerators = ImageGeneratorFactory::getImageGenerators();

        $mimeTypes = collect($this->option('types'))
            ->map(fn ($type) => $imageGenerators->first(fn ($generator) => $generator->getType() === $type))
            ->reject(fn ($generator) => is_null($generator))
            ->map(fn ($generator) => $generator->supportedMimeTypes())
            ->flatten()
            ->values();

        $mediaFiles = Media::with('model')
            ->when($mimeTypes->isNotEmpty(), fn ($query) => $query
                ->whereIn('mime_type', $mimeTypes))
            ->get();

        $progressBar = $this->output->createProgressBar($mediaFiles->count());

        $mediaFiles->each(function (Media $media) use ($progressBar) {
            $withResponsiveImages = $this->option('with-responsive-images');

            if (!$withResponsiveImages) {
                $withResponsiveImages = app(Image::class)->supportedMimeTypes()->contains($media->mime_type);
            }

            (new PerformRegenerationAction)->execute(
                media: $media,
                withResponsiveImages: $withResponsiveImages,
            );

            $progressBar->advance();
        });

        $progressBar->finish();

        return 0;
    }
}
