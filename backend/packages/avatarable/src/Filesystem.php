<?php

namespace Packages\Avatarable;

use Illuminate\Contracts\Filesystem\Factory;
use Packages\Avatarable\Models\Avatar;

class Filesystem
{
    protected $filesystem;

    public function __construct(Factory $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function storeFile(string $pathToFile, Avatar $avatar, ?string $fileName = null)
    {
        $destinationFileName = $fileName ?: pathinfo($pathToFile, PATHINFO_BASENAME);

        $destination = (new PathGenerator)->getPath($avatar) . $destinationFileName;

        $file = fopen($pathToFile, 'r');

        if ($avatar->getDiskDriverName() === 'local') {
            $this->filesystem
                ->disk($avatar->disk)
                ->put($destination, $file);

            fclose($file);

            return;
        }

        if (is_resource($file)) {
            fclose($file);
        }
    }

    public function deleteFile(Avatar $avatar, ?string $fileName = null)
    {
        $disk = $this->filesystem->disk($avatar->disk);
        $filePath = (new PathGenerator)->getPath($avatar) . ($fileName ?? $avatar->file_name);

        if ($disk->exists($filePath) && !is_dir($filePath)) {
            $disk->delete($filePath);
        }
    }
}
