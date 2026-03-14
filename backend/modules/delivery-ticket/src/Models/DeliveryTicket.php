<?php

declare(strict_types=1);

namespace Modules\DeliveryTicket\Models;

use App\Enums\DeliveryTicketType;
use App\Enums\InvoiceStatus;
use App\Models\Addressbook;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Place;
use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CanBeInvoiceItem;
use App\Models\Relations\CompanyOwned;
use App\Models\Relations\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Keygen\Keygen;
use Modules\DeliveryTicket\Database\Factories\DeliveryTicketFactory;

class DeliveryTicket extends Model
{
    use AuthoredByUser;
    use CanBeInvoiceItem;
    use CompanyOwned;
    use HasFactory;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_date',
        'internal_id',
        'external_id',
        'type',
        'is_paid',
        'total_selling_price',
        'total_purchasing_price',
        'delivery_address_id',
        'deliverer_id',
        'company_id',
    ];

    protected $searchable = [
        'columns' => [
            'internal_id',
            'external_id',
            'delivery_date',
        ],
        'relations' => [
            'order',
        ],
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'delivery_date' => 'datetime',
        'type' => DeliveryTicketType::class,
        'is_paid' => 'boolean',
    ];

    protected static function newFactory(): DeliveryTicketFactory
    {
        return DeliveryTicketFactory::new();
    }

    protected static function booted()
    {
        static::created(function (self $ticket) {
            $now = now();
            $ticketNumber = DeliveryTicket::query()
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->count()
                + 1;

            $number = Str::of($ticketNumber)->padLeft(4, '0');
            $random = Keygen::numeric(3)->generate();
            $ticket->internal_id = "{$now->year}{$now->month}-{$random}-{$number}";
            $ticket->save();
        });
    }

    public function scopeIncoming(Builder $query)
    {
        return $query->where('type', DeliveryTicketType::INCOMING());
    }

    public function scopeOutgoing(Builder $query)
    {
        return $query->where('type', DeliveryTicketType::OUTGOING());
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'delivery_address_id');
    }

    /** @return BelongsTo|Order */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo|Addressbook */
    public function deliverer(): BelongsTo
    {
        return $this->belongsTo(Addressbook::class, 'deliverer_id');
    }

    /** @return HasMany|DeliveryTicketDocument */
    public function documents(): HasMany
    {
        return $this->hasMany(DeliveryTicketDocument::class, 'delivery_ticket_id');
    }

    /** @return HasMany|OrderProduct */
    public function materials(): HasMany
    {
        return $this->hasMany(DeliveryTicketMaterial::class);
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class);
    }

    public function draftInvoice(): BelongsToMany
    {
        return $this->invoices()
            ->where('status', InvoiceStatus::DRAFT);
    }
}
