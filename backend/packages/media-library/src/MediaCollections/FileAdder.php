<?php

namespace Packages\MediaLibrary\MediaCollections;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Packages\MediaLibrary\Conversions\ImageGenerators\Image as ImageGenerator;
use Packages\MediaLibrary\HasMedia;
use Packages\MediaLibrary\MediaCollections\Exceptions\DiskCannotBeAccessed;
use Packages\MediaLibrary\MediaCollections\Exceptions\DiskDoesNotExist;
use Packages\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Packages\MediaLibrary\MediaCollections\Exceptions\UnknownType;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob;
use Packages\MediaLibrary\Support\File;
use Packages\MediaLibrary\Support\FileNamer;

class FileAdder
{
    /** @var UploadedFile|string */
    protected $file;

    protected ?HasMedia $subject = null;

    protected array $customProperties = [];

    protected string $pathToFile = '';

    protected ?Filesystem $filesystem;

    protected string $fileName = '';

    protected string $directory = '';

    protected string $mediaName = '';

    protected bool $generateResponsiveImages = false;

    public bool $preserveOriginal = false;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function setSubject(HasMedia $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function setFile($file): self
    {
        $this->file = $file;

        if (is_string($file)) {
            $this->pathToFile = $file;
            $this->setFileName(pathinfo($file, PATHINFO_BASENAME));
            $this->mediaName = pathinfo($file, PATHINFO_FILENAME);

            return $this;
        }

        if ($file instanceof UploadedFile) {
            $this->pathToFile = $file->getPath() . '/' . $file->getFilename();
            $this->setFileName($file->getClientOriginalName());
            $this->mediaName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            return $this;
        }

        throw new UnknownType;
    }

    public function setName(string $name): self
    {
        $this->mediaName = $name;

        return $this;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function withCustomProperties(array $customProperties): self
    {
        $this->customProperties = $customProperties;

        return $this;
    }

    public function withResponsiveImages(): self
    {
        $this->generateResponsiveImages = true;

        return $this;
    }

    public function withResponsiveImagesIf($condition): self
    {
        $this->generateResponsiveImages = (bool) (is_callable($condition) ? $condition() : $condition);

        return $this;
    }

    public function save(string $directory = '', string $diskName = ''): Media
    {
        $sanitizedFileName = $this->sanitizeFileName($this->fileName);
        $fileName = app(FileNamer::class)->originalFileName($sanitizedFileName);
        $this->fileName = $this->appendExtension($fileName, pathinfo($sanitizedFileName, PATHINFO_EXTENSION));

        if (!is_file($this->pathToFile)) {
            throw FileDoesNotExist::create($this->pathToFile);
        }

        $media = new Media;

        $media->name = $this->mediaName;
        $media->file_name = $this->fileName;

        $media->disk = $this->determineDiskName($diskName);
        $this->ensureDiskExists($media->disk);

        $media->directory = $this->normalizeDirectory($directory);

        $media->mime_type = File::getMimeType($this->pathToFile);
        $media->size = filesize($this->pathToFile);

        $media->generated_conversions = [];
        $media->custom_properties = $this->customProperties;

        $this->attachMedia($media);

        return $media;
    }

    protected function attachMedia(Media $media)
    {
        if (!$this->subject->exists) {
            $this->subject->prepareToAttachMedia($media, $this);

            $class = get_class($this->subject);

            $class::created(function ($model) {
                $model->processUnattachedMedia(function (Media $media, self $fileAdder) use ($model) {
                    $this->processMediaItem($model, $media, $fileAdder);
                });
            });

            return;
        }

        $this->processMediaItem($this->subject, $media, $this);
    }

    protected function processMediaItem(HasMedia $model, Media $media, self $fileAdder)
    {
        $model->mediaFiles()->save($media);

        $addedMediaSuccessfully = $this->filesystem->add($fileAdder->pathToFile, $media, $fileAdder->fileName);

        if (!$addedMediaSuccessfully) {
            $media->forceDelete();

            throw DiskCannotBeAccessed::create($media->disk);
        }

        if (!$fileAdder->preserveOriginal) {
            unlink($fileAdder->pathToFile);
        }

        if ($this->generateResponsiveImages && (new ImageGenerator)->canConvert($media)) {
            $job = new GenerateResponsiveImagesJob($media);

            if ($customConnection = config('media-library.queue_connection_name')) {
                $job->onConnection($customConnection);
            }

            if ($customQueue = config('media-library.queue_name')) {
                $job->onQueue($customQueue);
            }

            dispatch($job);
        }
    }

    public function preservingOriginal(bool $preserveOriginal = true): self
    {
        $this->preserveOriginal = $preserveOriginal;

        return $this;
    }

    protected function ensureDiskExists(string $diskName)
    {
        if (is_null(config("filesystems.disks.{$diskName}"))) {
            throw new DiskDoesNotExist;
        }
    }

    public function sanitizeFileName(string $fileName): string
    {
        return str_replace(['#', '/', '\\', ' '], '-', $fileName);
    }

    public function normalizeDirectory(string $directory): string
    {
        if (Str::startsWith($directory, '/')) {
            $directory = substr($directory, 1, strlen($directory));
        }

        if (Str::endsWith($directory, '/')) {
            $directory = substr($directory, 0, -1);
        }

        return $directory;
    }

    protected function determineDiskName(string $diskName): string
    {
        return $diskName !== '' ? $diskName : config('media-library.disk_name');
    }

    protected function appendExtension(string $file, ?string $extension): string
    {
        return $extension
            ? $file . '.' . $extension
            : $file;
    }
}
