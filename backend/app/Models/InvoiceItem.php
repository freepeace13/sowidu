<?php

namespace App\Models;

use App\Models\Relations\AuthoredByUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Modules\WorkLogs\Models\WorkLog;
use Znck\Eloquent\Traits\BelongsToThrough;

class InvoiceItem extends Model
{
    use AuthoredByUser;
    use BelongsToThrough;
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
        'quantity' => 'double',
    ];

    public function isDeliveryTicket(): bool
    {
        return same_morph_alias(DeliveryTicket::class, $this->item_type);
    }

    public function isDeliveryTicketMaterial(): bool
    {
        return same_morph_alias(DeliveryTicketMaterial::class, $this->item_type);
    }

    public function isOrderProduct(): bool
    {
        return same_morph_alias(OrderProduct::class, $this->item_type);
    }

    public function isWorkLog(): bool
    {
        return same_morph_alias(WorkLog::class, $this->item_type);
    }

    /** @return BelongsTo|Invoice */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphTo|DeliveryTicket|OrderProduct|DeliveryTicketMaterial */
    public function item()
    {
        return $this->morphTo();
    }

    public function company()
    {
        return $this->belongsToThrough(Company::class, Invoice::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeSubtotal($query)
    {
        return $query->select(DB::raw('SUM(quantity * price) as subtotal'));
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->price;
    }
}
