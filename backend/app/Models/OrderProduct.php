<?php

namespace App\Models;

use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CanBeInvoiceItem;
use App\Models\Relations\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class OrderProduct extends Model
{
    use AuthoredByUser;
    use CanBeInvoiceItem;
    use CompanyOwned;
    use HasFactory;

    protected $fillable = [
        'quantity',
        'is_paid',
        'details',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'double',
        'is_paid' => 'boolean',
        'details' => 'object',
    ];

    /** @return BelongsTo|Order */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo|Invoice */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return MorphOne|InvoiceItem */
    public function invoiceItem(): MorphOne
    {
        return $this->morphOne(InvoiceItem::class, 'item');
    }

    /** @return BelongsTo|DeliveryTicketMaterial */
    public function deliveryTicketMaterial(): BelongsTo
    {
        return $this->belongsTo(DeliveryTicketMaterial::class);
    }

    public function isDeliveryTicketMaterial(): bool
    {
        return filled($this->delivery_ticket_material_id);
    }

    public function getInvoice(): ?Invoice
    {
        if ($this->isDeliveryTicketMaterial()) {
            return $this?->deliveryTicketMaterial?->invoiceItem?->invoice;
        }

        return $this?->invoiceItem?->invoice;
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(\Modules\Offer\Models\Offer::class, 'offer_id');
    }
}
