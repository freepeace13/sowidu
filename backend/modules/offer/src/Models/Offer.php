<?php

namespace Modules\Offer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Offer\Database\Factories\OfferFactory;
use Modules\Offer\Enums\OfferStatus;
use Modules\Offer\Enums\OfferType;
use Modules\Offer\Models\Traits\AuthoredByUser;
use Modules\Offer\Models\Traits\CompanyOwned;
use Modules\Offer\Models\Traits\QueryHelper;
use Modules\Offer\Support\OfferProperties;

class Offer extends Model
{
    use AuthoredByUser;
    use CompanyOwned;
    use HasFactory;
    use QueryHelper;

    protected $fillable = [
        'uuid',
        'order_id',
        'internal_id',
        'recipientable_id',
        'recipientable_type',
        'title',
        'description',
        'type',
        'status',
        'offer_date',
        'subtotal',
        'net_amount',
        'total_vat',
        'grand_total',
        'subject',
        'message',
        'notes',
        'construction_site_id',
        'execution_period_start',
        'execution_period_end',
        'properties',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'offer_date' => 'datetime:Y-m-d',
        'type' => OfferType::class,
        'status' => OfferStatus::class,
        'execution_period_start' => 'datetime:Y-m-d',
        'execution_period_end' => 'datetime:Y-m-d',
        'properties' => 'collection',
    ];

    protected $attributes = [
        'properties' => '[]',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (blank($model->uuid)) {
                $model->uuid = Str::orderedUuid();
            }

            if (blank($model->internal_id)) {
                $model->internal_id = 'TMP-' . now()->getTimestamp();
            }
        });
    }

    protected static function newFactory()
    {
        return OfferFactory::new();
    }

    public function authCan(string $permission, ?Model $user = null): bool
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable&Model */
        $user ??= auth()->user();

        return $user?->can($permission, $this) ?? false;
    }

    public function isDraft(): bool
    {
        return $this->status === OfferStatus::DRAFT;
    }

    public function isSent(): bool
    {
        return $this->isPending();
    }

    public function isPending(): bool
    {
        return $this->status === OfferStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === OfferStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === OfferStatus::REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === OfferStatus::CANCELLED;
    }

    public function scopeExceptDraft(Builder $query)
    {
        return $query->whereNot('status', OfferStatus::DRAFT);
    }

    public function scopeFindByIdOrFail(Builder $query, $id)
    {
        return $query->where(function ($query) use ($id) {
            $query->where('id', $id)
                ->orWhere('uuid', $id);
        })->firstOrFail();
    }

    public function recipientable()
    {
        return $this->morphTo();
    }

    public function recipient()
    {
        return $this->morphTo(__FUNCTION__, 'recipientable_type', 'recipientable_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfferItem::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(OfferHistory::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo($this->getOrderModelClass());
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(OfferDeduction::class);
    }

    public function constructionSite(): BelongsTo
    {
        return $this->belongsTo(
            $this->getPlaceModelClass(),
            'construction_site_id',
        );
    }

    public function properties(): OfferProperties
    {
        return new OfferProperties($this);
    }

    public function orderUrl(): string
    {
        if (!$this->order_id) {
            return '';
        }

        return route('orders.show', [
            'order' => $this->order_id,
        ]);
    }

    protected function getOrderModelClass(): string
    {
        return config('offer.models.order', \App\Models\Order::class);
    }

    protected function getPlaceModelClass(): string
    {
        return config('offer.models.place', \App\Models\Place::class);
    }
}
