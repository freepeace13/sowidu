<?php

namespace Packages\Translation\Readers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\SplFileInfo;

class FileReader implements ReaderInterface
{
    protected $disks;

    protected $langPath;

    protected $items = [];

    public function __construct(Filesystem $disks, string $langPath)
    {
        $this->disks = $disks;
        $this->langPath = $langPath;
    }

    public function read()
    {
        $locales = $this->disks->directories($this->langPath);

        foreach ($locales as $locale) {
            foreach ($this->disks->files($locale) as $file) {
                $this->mapGroups($file);
            }
        }

        return $this->items;
    }

    protected function parseLines(SplFileInfo $file)
    {
        return $this->disks->getRequire($file->getPathname());
    }

    protected function parseLocale(SplFileInfo $file)
    {
        return basename($file->getPath());
    }

    protected function parseGroup(SplFileInfo $file)
    {
        return $file->getFilenameWithoutExtension();
    }

    protected function mapLines($namespace, string $locale, array $lines)
    {
        foreach ($lines as $key => $line) {
            $deeper = "{$namespace}.{$key}";

            if (is_array($line)) {
                $this->mapLines("{$deeper}", $locale, $line);
            }

            if (is_string($line)) {
                Arr::set($this->items, "{$deeper}.{$locale}", $line);
            }
        }
    }

    protected function mapGroups(SplFileInfo $file)
    {
        $this->mapLines(
            $this->parseGroup($file),
            $this->parseLocale($file),
            $this->parseLines($file),
        );
    }
}
