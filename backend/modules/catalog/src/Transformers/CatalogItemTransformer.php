<?php

namespace Modules\Catalog\Transformers;

use Modules\Catalog\Models\CatalogItemType;
use Modules\Shared\Transformer;
use Packages\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property \Modules\Catalog\Models\CatalogItem $resource
 */
class CatalogItemTransformer extends Transformer
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'internal_id' => $this->resource->internal_id,
            'vendor_id' => $this->resource->vendor_id,
            'manufacture_id' => $this->resource->manufacture_id,
            'unit' => $this->resource->unit,
            'unit_name' => $this->resource->unit_name,
            'purchasing_price' => $this->resource->purchasing_price,
            'selling_price' => $this->resource->selling_price,
            'description' => $this->resource->description,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }

    public function withSellingPrice(bool $canViewSellingPrice)
    {
        return $this->state(fn () => [
            'selling_price' => $canViewSellingPrice ? $this->resource->selling_price : '--',
        ]);
    }

    public function withPurchasingPrice(bool $canViewPurchasingPrice)
    {
        return $this->state(fn () => [
            'purchasing_price' => $canViewPurchasingPrice ? $this->resource->purchasing_price : '--',
        ]);
    }

    public function withMedia(?Media $media = null)
    {
        return $this->state(fn () => [
            'media' => [
                'url' => $media ? $media->getUrl() : null,
                'thumbnail_url' => $media ? array_first($media->getConversionUrls()) : null,
                'id' => $media ? $media->getKey() : null,
            ],
        ]);
    }

    public function withType(CatalogItemType $type)
    {
        return $this->state(fn () => [
            'type' => [
                'id' => $type->id,
                'name' => $type->name,
            ],
        ]);
    }
}
