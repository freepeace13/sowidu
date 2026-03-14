<?php

namespace Packages\MediaLibrary\Conversions;

use BadMethodCallException;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\Support\FileNamer;
use Spatie\Image\Manipulations;

class Conversion
{
    protected $name = '';

    protected FileNamer $fileNamer;

    protected $extractVideoFrameAtSecond = 0;

    protected bool $generateResponsiveImages = false;

    protected Manipulations $manipulations;

    protected bool $performOnQueue = false;

    protected bool $keepOriginalImageFormat = false;

    protected $pdfPageNumber = 1;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->manipulations = (new Manipulations)
            ->optimize(config('media-library.image_optimizers'))
            ->format(Manipulations::FORMAT_JPG);

        $this->fileNamer = app(FileNamer::class);
    }

    public static function create(string $name)
    {
        return new static($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function pdfPageNumber(int $pageNumber): self
    {
        $this->pdfPageNumber = $pageNumber;

        return $this;
    }

    public function getPdfPageNumber(): int
    {
        return $this->pdfPageNumber;
    }

    public function extractVideoFrameAtSecond(float $timeCode): self
    {
        $this->extractVideoFrameAtSecond = $timeCode;

        return $this;
    }

    public function getExtractVideoFrameAtSecond(): float
    {
        return $this->extractVideoFrameAtSecond;
    }

    public function keepOriginalImageFormat(): self
    {
        $this->keepOriginalImageFormat = true;

        return $this;
    }

    public function shouldKeepOriginalImageFormat(): bool
    {
        return $this->keepOriginalImageFormat;
    }

    public function getManipulations(): Manipulations
    {
        return $this->manipulations;
    }

    public function removeManipulation(string $manipulationName): self
    {
        $this->manipulations->removeManipulation($manipulationName);

        return $this;
    }

    public function withResponsiveImages(): self
    {
        $this->generateResponsiveImages = true;

        return $this;
    }

    public function shouldGenerateResponsiveImages(): bool
    {
        return $this->generateResponsiveImages;
    }

    public function withoutManipulations(): self
    {
        $this->manipulations = new Manipulations;

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (!method_exists($this->manipulations, $name)) {
            throw new BadMethodCallException("Manipulation `{$name}` does not exist");
        }

        $this->manipulations->$name(...$arguments);

        return $this;
    }

    public function setManipulations($manipulations): self
    {
        if ($manipulations instanceof Manipulations) {
            $this->manipulations = $this->manipulations->mergeManipulations($manipulations);
        }

        if (is_callable($manipulations)) {
            $manipulations($this->manipulations);
        }

        return $this;
    }

    public function addAsFirstManipulations(Manipulations $manipulations): self
    {
        $manipulationSequence = $manipulations->getManipulationSequence()->toArray();

        $this->manipulations
            ->getManipulationSequence()
            ->mergeArray($manipulationSequence);

        return $this;
    }

    public function queued(): self
    {
        $this->performOnQueue = true;

        return $this;
    }

    public function nonQueued(): self
    {
        $this->performOnQueue = false;

        return $this;
    }

    public function nonOptimized(): self
    {
        $this->removeManipulation('optimize');

        return $this;
    }

    public function shouldBeQueued(): bool
    {
        return $this->performOnQueue;
    }

    public function getResultExtension(string $originalFileExtension = ''): string
    {
        if ($this->shouldKeepOriginalImageFormat()) {
            if (in_array($originalFileExtension, ['jpg', 'jpeg', 'pjpg', 'png', 'gif'])) {
                return $originalFileExtension;
            }
        }

        if ($manipulationArgument = $this->manipulations->getManipulationArgument('format')) {
            return $manipulationArgument;
        }

        return $originalFileExtension;
    }

    public function getConversionFile(Media $media): string
    {
        $fileName = $this->fileNamer->conversionFileName($media->file_name, $this);

        $fileExtension = $this->fileNamer->extensionFromBaseImage($media->file_name);
        $extension = $this->getResultExtension($fileExtension) ?: $fileExtension;

        return "{$fileName}.{$extension}";
    }
}
