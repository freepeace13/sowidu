<?php

namespace Packages\MediaLibrary\MediaCollections\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Packages\MediaLibrary\Conversions\Conversion;
use Packages\MediaLibrary\Conversions\ConversionCollection;
use Packages\MediaLibrary\Conversions\ImageGenerators\ImageGeneratorFactory;
use Packages\MediaLibrary\MediaCollections\Filesystem;
use Packages\MediaLibrary\MediaCollections\MediaFilter;
use Packages\MediaLibrary\MediaCollections\Models\Concerns\HasAddressTags;
use Packages\MediaLibrary\MediaCollections\Models\Concerns\HasCustomProperties;
use Packages\MediaLibrary\MediaCollections\Models\Concerns\HasMetaData;
use Packages\MediaLibrary\MediaCollections\Models\Concerns\HasUuid;
use Packages\MediaLibrary\MediaCollections\Models\Concerns\Shareable;
use Packages\MediaLibrary\ResponsiveImages\RegisteredResponsiveImages;
use Packages\MediaLibrary\Support\File;
use Packages\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;

class Media extends Model
{
    const TYPE_OTHER = 'other';

    use Filterable,
        HasAddressTags,
        HasCustomProperties,
        HasMetaData,
        HasUuid,
        Shareable,
        SoftDeletes;

    protected $table = 'media_files';

    protected $guarded = [];

    protected $casts = [
        'custom_properties' => 'array',
        'generated_conversions' => 'array',
        'responsive_images' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function (self $media) {
            if (in_array(SoftDeletes::class, class_uses_recursive($media))) {
                if (!$media->isForceDeleting()) {
                    return;
                }
            }

            // Clean up media associates before permanently deleting
            $media->addressTags()
                ->delete();
            $media->shares()
                ->delete();
        });

        static::deleted(function (self $media) {
            if (in_array(SoftDeletes::class, class_uses_recursive($media))) {
                if (!$media->isForceDeleting()) {
                    return;
                }
            }

            $filesystem = app(Filesystem::class);
            $filesystem->removeAllFiles($media);
        });
    }

    public function modelFilter()
    {
        return $this->provideFilter(MediaFilter::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function isRoot()
    {
        return is_null($this->folder_id);
    }

    public function isFolder()
    {
        return false;
    }

    public function depth()
    {
        $depth = 1;
        $parent = $this->folder;

        while ($parent->exists) {
            $depth += 1;
            $parent = $parent->folder;
        }

        return $depth;
    }

    public function getUrl(string $conversionName = ''): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);

        return $urlGenerator->getUrl();
    }

    public function getPath(string $conversionName = '', bool $relative = false): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);

        return $urlGenerator->getPath($relative);
    }

    public function getHumanReadableSizeAttribute(): string
    {
        return File::getHumanReadableSize($this->size);
    }

    public function getTypeAttribute(): string
    {
        $type = $this->getTypeFromExtension();

        if ($type !== self::TYPE_OTHER) {
            return $type;
        }

        return $this->getTypeFromMime();
    }

    public function getTypeFromExtension(): string
    {
        $imageGenerator = ImageGeneratorFactory::forExtension($this->extension);

        return $imageGenerator
            ? $imageGenerator->getType()
            : static::TYPE_OTHER;
    }

    public function getTypeFromMime(): string
    {
        $imageGenerator = ImageGeneratorFactory::forMimeType($this->mime_type);

        return $imageGenerator
            ? $imageGenerator->getType()
            : static::TYPE_OTHER;
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getDiskDriverName(): string
    {
        return strtolower(config("filesystems.disks.{$this->disk}.driver"));
    }

    public function getConversionUrls(): array
    {
        $conversions = ConversionCollection::createForMedia($this);

        return $conversions
            ->mapWithKeys(function (Conversion $conversion) {
                return [$conversion->getName() => $this->getUrl($conversion->getName())];
            })
            ->toArray();
    }

    public function getConversionNames(): array
    {
        $conversions = ConversionCollection::createForMedia($this);

        return $conversions
            ->map(function (Conversion $conversion) {
                return $conversion->getName();
            })
            ->toArray();
    }

    public function getGeneratedConversions(): Collection
    {
        return new Collection($this->generated_conversions ?? []);
    }

    public function markAsConversionGenerated(string $conversionName): self
    {
        $generatedConversions = $this->generated_conversions;

        Arr::set($generatedConversions, $conversionName, true);

        $this->generated_conversions = $generatedConversions;

        $this->save();

        return $this;
    }

    public function markAsConversionNotGenerated(string $conversionName): self
    {
        $generatedConversions = $this->generated_conversions;

        Arr::set($generatedConversions, $conversionName, false);

        $this->generated_conversions = $generatedConversions;

        $this->save();

        return $this;
    }

    public function hasGeneratedConversion(string $conversionName): bool
    {
        $generatedConversions = $this->getGeneratedConversions();

        return $generatedConversions[$conversionName] ?? false;
    }

    public function getResponsiveImageUrls(string $conversionName = ''): array
    {
        return $this->responsiveImages($conversionName)
            ->getUrls();
    }

    public function hasResponsiveImages(string $conversionName = ''): bool
    {
        return count($this->getResponsiveImageUrls($conversionName)) > 0;
    }

    public function getSrcset(string $conversionName = ''): string
    {
        return $this->responsiveImages($conversionName)
            ->getSrcset();
    }

    public function responsiveImages(string $conversionName = ''): RegisteredResponsiveImages
    {
        return new RegisteredResponsiveImages($this, $conversionName);
    }

    /**
     * Get the category
     *
     * @param  string  $value
     * @return string
     */
    public function getCategoryAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Set the Category
     *
     * @param  string  $value
     * @return void
     */
    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = strtolower($value);
    }
}
