<?php

namespace Modules\Catalog\Models;

use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CompanyOwned;
use App\Models\Relations\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class CatalogItem extends Model
{
    use AuthoredByUser;
    use CompanyOwned;
    use Filterable;
    use HasFactory;
    use Searchable;

    protected static function booted()
    {
        static::saving(function ($catalogItem) {
            // Ensure internal_id is never null - use temporary value if not set
            if (empty($catalogItem->internal_id)) {
                $catalogItem->internal_id = 'SW-TEMP-' . uniqid();
            }
        });

        static::saved(function ($catalogItem) {
            // Update internal_id if it's a temporary value
            if (str_starts_with($catalogItem->internal_id, 'SW-TEMP-')) {
                $catalogItem->updateQuietly([
                    'internal_id' => 'SW-' . crc32($catalogItem->id),
                ]);
            }
        });
    }

    protected $fillable = [
        'name',
        'internal_id',
        'vendor_id',
        'manufacture_id',
        'unit',
        'unit_name',
        'purchasing_price',
        'selling_price',
        'description',
    ];

    protected $searchable = [
        'columns' => [
            'name',
            'internal_id',
            'vendor_id',
            'manufacture_id',
            'unit',
            'selling_price',
            'purchasing_price',
            'description',
        ],
    ];

    protected static function newFactory()
    {
        return \Modules\Catalog\Database\Factories\CatalogItemFactory::new();
    }

    public function type()
    {
        return $this->belongsTo(CatalogItemType::class, 'catalog_item_type_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Media */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
