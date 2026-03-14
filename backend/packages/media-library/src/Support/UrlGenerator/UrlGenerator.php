<?php

namespace Packages\MediaLibrary\Support\UrlGenerator;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Adapter\AbstractAdapter;
use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Packages\MediaLibrary\Support\PathGenerator;

class UrlGenerator
{
    protected $media;

    protected $conversion = null;

    protected $pathGenerator;

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function setMedia(Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getUrl(): string
    {
        $path = Str::after($this->getPathRelativeToRoot(), $this->media->uuid . '/');

        return $this->getPathUrl($path);
    }

    public function getPath(bool $relative = false): string
    {
        return ($relative ? '' : $this->getPathPrefix()) . $this->getPathRelativeToRoot();
    }

    protected function getPathPrefix()
    {
        $adapter = $this->getDisk()->getAdapter();

        $cachedAdapter = '\League\Flysystem\Cached\CachedAdapter';

        if ($adapter instanceof $cachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = '';
        if ($adapter instanceof AbstractAdapter) {
            $pathPrefix = $adapter->getPathPrefix();
        }

        return $pathPrefix;
    }

    public function getPathUrl(string $path)
    {
        $url = route('media-library', [
            'media' => $this->media->uuid,
            'path' => $path,
        ]);

        $url = $this->versionUrl($url);

        return $url;
    }

    public function setConversion(Conversion $conversion): self
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function setPathGenerator(PathGenerator $pathGenerator): self
    {
        $this->pathGenerator = $pathGenerator;

        return $this;
    }

    public function getPathRelativeToRoot(): string
    {
        if (is_null($this->conversion)) {
            return $this->pathGenerator->getPath($this->media) . ($this->media->file_name);
        }

        return $this->pathGenerator->getPathForConversions($this->media)
            . $this->conversion->getConversionFile($this->media);
    }

    protected function getDisk(): Filesystem
    {
        return Storage::disk($this->media->disk);
    }

    public function versionUrl(string $path = ''): string
    {
        if (!$this->config->get('media-library.version_urls')) {
            return $path;
        }

        return "{$path}?v={$this->media->updated_at->timestamp}";
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(Str::after($path, $this->media->uuid . '/'), '/');
    }

    public function getResponsiveImagePath(): string
    {
        return Str::finish($this->pathGenerator->getPathForResponsiveImages($this->media), '/');
    }
}
