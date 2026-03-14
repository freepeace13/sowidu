<?php

namespace Packages\MediaLibrary\Conversions\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Packages\MediaLibrary\Conversions\ConversionCollection;
use Packages\MediaLibrary\Conversions\FileManipulator;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class PerformConversionsJob implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    protected $conversions;

    protected $media;

    public function __construct(ConversionCollection $conversions, Media $media)
    {
        $this->conversions = $conversions;

        $this->media = $media;
    }

    public function handle(FileManipulator $fileManipulator): bool
    {
        $fileManipulator->performConversions($this->media, $this->conversions);

        return true;
    }
}
