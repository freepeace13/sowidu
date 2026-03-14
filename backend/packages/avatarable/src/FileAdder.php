<?php

namespace Packages\Avatarable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Packages\Avatarable\Exceptions\DiskDoesNotExist;
use Packages\Avatarable\Exceptions\FileDoesNotExist;
use Packages\Avatarable\Exceptions\UnknownType;
use Packages\Avatarable\Models\Avatar;

class FileAdder
{
    protected $fileToPath;

    public $fileName;

    protected $subject;

    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setSubject(Model $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function setFile($file): self
    {
        if (is_string($file)) {
            $this->pathToFile = $file;
            $this->fileName = pathinfo($file, PATHINFO_BASENAME);

            return $this;
        }

        if ($file instanceof UploadedFile) {
            $this->pathToFile = $file->getPath() . '/' . $file->getFilename();
            $this->fileName = $file->getClientOriginalName();

            return $this;
        }

        throw UnknownType::create();
    }

    public function save(string $diskName = ''): Avatar
    {
        $sanitizedFileName = $this->sanitizeFileName($this->fileName);

        $fileName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);

        $this->fileName = $this->appendExtension($fileName, $extension);

        if (!is_file($this->pathToFile)) {
            throw FileDoesNotExist::create($this->pathToFile);
        }

        $avatar = new Avatar;

        $avatar->file_name = $this->fileName;

        $avatar->disk = $this->determineDiskName($diskName);
        $this->ensureDiskExists($avatar->disk);

        $this->processAvatarFile($this->subject, $avatar, $this);

        return $avatar;
    }

    protected function processAvatarFile(Model $model, Avatar $avatar, self $fileAdder)
    {
        if ($model->avatar()->exists()) {
            $model->avatar()->delete();
            $this->filesystem->deleteFile($model->avatar);
        }

        $model->avatar()->save($avatar);

        $this->filesystem->storeFile(
            $fileAdder->pathToFile,
            $avatar,
            $fileAdder->fileName,
        );
    }

    protected function sanitizeFileName(string $fileName): string
    {
        return str_replace(['#', '/', '\\', ' '], '-', $fileName);
    }

    protected function normalizeDirectory(string $directory): string
    {
        if (Str::startsWith($directory, '/')) {
            $directory = substr($directory, 1, strlen($directory));
        }

        if (Str::endsWith($directory, '/')) {
            $directory = substr($directory, 0, -1);
        }

        return $directory;
    }

    protected function appendExtension(string $file, ?string $extension): string
    {
        return $extension
            ? $file . '.' . $extension
            : $file;
    }

    protected function ensureDiskExists(string $diskName)
    {
        if (is_null(config("filesystems.disks.{$diskName}"))) {
            throw DiskDoesNotExist::create($diskName);
        }
    }

    protected function determineDiskName(string $diskName): string
    {
        return $diskName !== '' ? $diskName : config('avatar.disk');
    }
}
