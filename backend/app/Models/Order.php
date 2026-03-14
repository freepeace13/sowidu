<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Concerns\HasDeliveryPlace;
use App\Models\Relations\HasLogs;
use App\Models\Relations\HasMediaAttachments;
use App\Models\Relations\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Offer\Models\Relations\HasOffer;
use Modules\WorkLogs\Models\WorkLog;

class Order extends Model
{
    use Filterable;
    use HasDeliveryPlace;
    use HasFactory;
    use HasLogs;
    use HasMediaAttachments;
    use HasOffer;
    use Searchable;
    use SoftDeletes;

    const INCOMING_TYPE = 1;
    const OUTGOING_TYPE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'team_id',
        'client_addressbook_id',
        'contractor_addressbook_id',
        'delivery_address_record_id',
        'order_number',
        'type',
        'description',
        'status',
        'order_date',
        'planned_start_date',
        'planned_finish_date',
    ];

    protected $searchable = [
        'columns' => ['description', 'order_number', 'order_date'],
        'relations' => ['client', 'contractor'],
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => OrderStatus::class,
    ];

    // TODO - REMOVE!
    protected $with = ['client'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->order_number)) {
                $model->order_number = 'TMP-' . now()->getTimestamp();
            }
        });

        static::deleted(function (self $model) {
            // Update order `status`
            $model->update([
                'order_number' => 'DEL-' . now()->getTimestamp(),
            ]);

            if ($model->isForceDeleting) {
                // Delete activity logs
                activity_log($model)->orderLog()
                    ->delete();
            }
        });
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', '!=', OrderStatus::IN_PREPARATION)
            ->where('status', '!=', OrderStatus::CANCELLED);
    }

    public function getTypeNameAttribute()
    {
        return $this->type === self::INCOMING_TYPE ? 'Incoming' : 'Outgoing';
    }

    public function client()
    {
        return $this->morphTo('client', 'clientable_type', 'clientable_id');
    }

    /** @return Company|\Illuminate\Database\Eloquent\Relations\MorphTo */
    public function contractor()
    {
        return $this->morphTo('contractor', 'contractable_type', 'contractable_id');
    }

    public function clientAddressbook()
    {
        return $this->hasOne(
            Addressbook::class,
            'id',
            'client_addressbook_id',
        );
    }

    public function contractorAddressbook()
    {
        return $this->hasOne(
            Addressbook::class,
            'id',
            'contractor_addressbook_id',
        );
    }

    public function userAuthor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function companyAuthor()
    {
        return $this->belongsTo(Company::class, 'team_id');
    }

    public function isOwnedByCompany(?Company $company = null): bool
    {
        if (!$this->companyAuthor) {
            return false;
        }

        if (blank($company)) {
            return false;
        }

        return $this->companyAuthor->is($company);
    }

    public function isContractor(?Company $company = null): bool
    {
        if (!$this->companyAuthor) {
            return false;
        }
        if (blank($company)) {
            return false;
        }

        return $this->contractor->is($company);

    }

    public function clientIs($model)
    {
        return $this->client->is($model);
    }

    public function contractorIs(?Company $company = null): bool
    {
        if (!$company) {
            return false;
        }

        return $this->loadMissing(['contractor'])
            ->contractor->is($company);
    }

    public function deliveryAddressRecord()
    {
        return $this->belongsTo(AddressRecord::class, 'id', 'delivery_address_record_id');
    }

    public function getOppositeParty(User $userCauser, ?Company $causerRepresenting)
    {
        $causer = $causerRepresenting ?? $userCauser;

        if ($this->contractor->is($causer)) {
            $this->loadMissing('client');

            // Causer is the contractor - opposite party is the `Client`
            return $this->client;
        }

        // Causer is the client - opposite party is `Contractor`
        $this->loadMissing('contractor');

        return $this->contractor;
    }

    /** @return HasMany|OrderProduct */
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /** @return HasMany|OrderProduct */
    public function usedProducts(): HasMany
    {
        return $this->products()
            ->whereNull('delivery_ticket_material_id');
    }

    /** @return HasMany|OrderProduct */
    public function usedMaterials(): HasMany
    {
        return $this->products()
            ->whereNotNull('delivery_ticket_material_id');
    }

    public function unPaidUsedProducts()
    {
        return $this->usedProducts()
            ->unPaid();
    }

    public function unPaidAndUnInvoicedUsedProducts()
    {
        return $this->usedProducts()
            ->unPaid()
            ->unInvoiced();
    }

    /** @return HasMany|DeliveryTicket */
    public function deliveryTickets(): HasMany
    {
        return $this->hasMany(DeliveryTicket::class);
    }

    public function deliveryTicketsMaterials(): HasManyThrough
    {
        return $this->hasManyThrough(
            DeliveryTicketMaterial::class,
            DeliveryTicket::class,
            'order_id',
            'delivery_ticket_id',
        );
    }

    /** @return HasMany|Invoice */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class, 'order_id');
    }
}
