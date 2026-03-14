<?php

namespace App\Models;

use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogItemType extends Model
{
    use AuthoredByUser;
    use CompanyOwned;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
    ];

    public function items()
    {
        return $this->hasMany(CatalogItem::class, 'catalog_item_type_id');
    }
}
