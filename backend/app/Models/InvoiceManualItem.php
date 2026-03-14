<?php

namespace App\Models;

use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceManualItem extends Model
{
    use AuthoredByUser;
    use CompanyOwned;
    use HasFactory;

    protected $fillable = [
        'name',
        'internal_id',
        'vendor_id',
        'unit_name',
        'quantity',
        'purchasing_price',
        'selling_price',
        'description',
    ];

    protected $casts = [
        'purchasing_price' => 'double',
        'selling_price' => 'double',
        'quantity' => 'double',
    ];

    public function type()
    {
        return $this->belongsTo(CatalogItemType::class, 'catalog_item_type_id');
    }
}
