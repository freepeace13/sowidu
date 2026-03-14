<?php

namespace App\Models;

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
