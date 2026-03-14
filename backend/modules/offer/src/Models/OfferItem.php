<?php

namespace Modules\Offer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Offer\Database\Factories\OfferItemFactory;
use Modules\Offer\Models\Traits\AuthoredByUser;

class OfferItem extends Model
{
    use AuthoredByUser;
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'description',
        'details',
    ];

    protected $casts = [
        'details' => 'collection',
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    protected static function newFactory()
    {
        return OfferItemFactory::new();
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function getSubTotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }
}
