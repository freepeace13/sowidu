<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Models;

use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CanBeInvoiceItem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\DeliveryTicket\Database\Factories\DeliveryTicketMaterialFactory;

class DeliveryTicketMaterial extends Model
{
    use AuthoredByUser;
    use CanBeInvoiceItem;
    use HasFactory;

    protected static function newFactory(): DeliveryTicketMaterialFactory
    {
        return DeliveryTicketMaterialFactory::new();
    }

    protected $fillable = [
        'quantity',
        'purchasing_price',
        'selling_price',
        'is_paid',
        'details',
    ];

    protected $touches = [
        'deliveryTicket',
    ];

    protected $casts = [
        'quantity' => 'double',
        'purchasing_price' => 'double',
        'selling_price' => 'double',
        'details' => 'collection',
        'is_paid' => 'boolean',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('details', 'like', '%' . $search . '%');
    }

    /** @return BelongsTo|DeliveryTicket */
    public function deliveryTicket(): BelongsTo
    {
        return $this->belongsTo(DeliveryTicket::class);
    }

    protected function purchasingPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?? data_get(
                $attributes['details'],
                'purchasing_price',
                0,
            ),
        );
    }

    protected function sellingPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $value ?? data_get(
                $attributes['details'],
                'selling_price',
                0,
            ),
        );
    }
}
