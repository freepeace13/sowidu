<?php

namespace App\Models;

use App\Enums\InvoiceKind;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\QueryBuilders\QueryHelper;
use App\Models\Relations\AuthoredByUser;
use App\Models\Relations\CompanyOwned;
use App\Models\Traits\FindableByUuid;
use App\Modules\Invoice\InvoiceService;
use App\Services\CacheService;
use App\Transformers\Addressbook\AddressbookTransformer;
use App\Transformers\CompanyTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use AuthoredByUser;
    use CompanyOwned;
    use FindableByUuid;
    use HasFactory;
    use QueryHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'biller_id',
        'biller_type',
        'delivery_date',
        'internal_id',
        'external_id',
        'type',
        'kind',
        'biller_details',
        'notes',
        'status',
        'subject',
        'description',
        'send_date',
        'payment_date',
        'final_data', // TODO - remove this column - @todo remove final_data
        'deduction_invoice_id',
        'execution_period_start',
        'execution_period_end',
        'construction_site_id',
        'care_of_id',
        'subtotal',
        'net_amount',
        'total_vat',
        'grand_total',
        'preview_layout', // TODO - remove this column - @todo remove preview_layout
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'delivery_date' => 'datetime:Y-m-d',
        'send_date' => 'datetime:Y-m-d',
        'payment_date' => 'datetime:Y-m-d',
        'type' => InvoiceType::class,
        'biller_details' => 'collection',
        'final_data' => 'collection',
        'status' => InvoiceStatus::class,
        'kind' => InvoiceKind::class,
        'preview_layout' => 'collection',
    ];

    // This will eager load the deductions relation everytime! Remove!
    // protected $with = ['deductions'];

    protected static function booted()
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::orderedUuid();
            }
        });

        static::created(function (self $invoice) {
            // Generate temporary invoice number
            InvoiceService::run($invoice)->saveTemporaryInternalId();

            // Generate biller details
            $biller = $invoice->loadMissing('biller')
                ->biller;

            $billerDetails = [];

            if (
                morph_is(
                    $biller,
                    Addressbook::class,
                )
            ) {
                $billerDetails = AddressbookTransformer::make($biller)
                    ->withAddress()
                    ->resolve();

            }

            if (
                morph_is(
                    $biller,
                    Company::class,
                )
            ) {
                $billerDetails = (new CompanyTransformer($biller))
                    ->withCurrentAddress($biller->currentPlace()
                        ->first())
                    ->resolve();
            }

            if (!empty($billerDetails)) {
                $invoice->update([
                    'biller_details' => $billerDetails,
                ]);
            }
        });
    }

    protected function isPaid(): Attribute
    {
        return Attribute::make(
            get: fn ($value,
                $attributes) => isset($attributes['status']) && $attributes['status'] == InvoiceStatus::PAID(),
        );
    }

    public function scopeNotDraft(Builder $query)
    {
        return $query
            ->where(
                'status',
                '!=',
                InvoiceStatus::DRAFT(),
            );
    }

    public function isDraft(): bool
    {
        return $this->status->value == InvoiceStatus::DRAFT->value;
    }

    public function isSent(): bool
    {
        return $this->status->value == InvoiceStatus::SENT->value;
    }

    public function isAlreadySent(): bool
    {
        return $this->status->value != InvoiceStatus::DRAFT->value;
    }

    public function isEditable(): bool
    {
        return $this->isDraft();
    }

    public function currency()
    {
        return CacheService::getCompanyCurrency($this->company_id);
    }

    public function scopeFindByIdOrUuid(Builder $query, string $identifier): Builder
    {
        return $query->where('uuid', $identifier)
            ->orWhere('id', $identifier);
    }

    public function scopeFullyPaid(Builder $query): Builder
    {
        return $query->where(
            'status',
            InvoiceStatus::PAID(),
        );
    }

    public function scopeWithPayments(Builder $query): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->where('status', InvoiceStatus::PAID())
                ->orWhere('status', InvoiceStatus::PARTIALLY_PAID())
                ->orWhere('status', InvoiceStatus::OVERPAID()),
        );
    }

    public function scopePartialInvoices(Builder $query): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->where('kind', InvoiceKind::PARTIAL_1)
                ->orWhere('kind', InvoiceKind::PARTIAL_2),
        );
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where(
            'status',
            InvoiceStatus::DRAFT(),
        );
    }

    public function scopeExceptDraft(Builder $query)
    {
        return $query->whereNot(
            'status',
            InvoiceStatus::DRAFT(),
        );
    }

    public function scopeInvoiced(Builder $query): Builder
    {
        return $query->where(
            'status',
            InvoiceStatus::SENT(),
        );
    }

    public function scopeIncoming(Builder $query)
    {
        return $query->where('type', InvoiceType::INCOMING());
    }

    public function scopeOutgoing(Builder $query)
    {
        return $query->where('type', InvoiceType::OUTGOING());
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(
            Place::class,
            'delivery_address_id',
        );
    }

    /** @return BelongsTo|Order */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphTo */
    public function biller()
    {
        return $this->morphTo('biller');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\MorphTo|Addressbook|Company */
    public function client()
    {
        return $this->biller();
    }

    public function documents()
    {
        return $this->morphMany(
            Attachment::class,
            'model',
        );
    }

    /** @return HasMany|InvoiceItem */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function deliveryTickets(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryTicket::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Company */
    public function contractor()
    {
        return $this->belongsTo(
            Company::class,
            'company_id',
        );
    }

    /**
     * Get the taxes associated with the invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Tax>
     */
    public function taxes(): BelongsToMany
    {
        return $this->belongsToMany(Tax::class)
            ->withTimestamps();
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)
            ->with('item')
            ->where(
                'item_type',
                'work_logs',
            );
    }

    public function aggregateWorkLogs(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)
            ->select(
                'user_id',
                'description',
                'id',
                DB::raw('FORMAT(price, 2) as price'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(details, "$.user.id")) as user_id'),
                DB::raw('FORMAT(SUM(quantity), 2) as qty'),
                // DB::raw('FORMAT(SUM(price), 2) as price'),
                DB::raw('FORMAT(SUM(quantity * price), 2) as subtotal'),
                DB::raw("CONCAT(
                    CASE
                        WHEN item_type = 'work_logs' THEN 'hours'
                    ELSE 'hours'
                    END
                ) AS unit"),
            )
            ->where(
                'item_type',
                'work_logs',
            )
            ->groupBy(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(details, "$.user.id"))'))
            ->with('user');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function careOf(): BelongsTo
    {
        return $this->belongsTo(Addressbook::class, 'care_of_id');
    }

    /** @return HasMany|\Illuminate\Support\Collection<InvoiceDeduction> */
    public function deductions(): HasMany
    {
        return $this->hasMany(InvoiceDeduction::class);
    }

    public function deductibleBy(): MorphTo
    {
        return $this->morphTo('deductible');
    }

    // public function deduction(): BelongsTo
    // {
    //     return $this->belongsTo(self::class, 'id');
    // }
    // public function deductions(): HasMany
    // {
    //     return $this->hasMany(self::class, 'deduction_invoice_id');
    // }

    public function isNotUsedAsInvoiceDeduction(): bool
    {
        return $this->deductible()
            ->doesntExist();
    }

    public function deductible()
    {
        return $this->morphOne(InvoiceDeduction::class, 'deductible');
    }

    public function manualDeductions(): HasMany
    {
        return $this->hasMany(DeductionManual::class, 'invoice_id');
    }

    public function constructionSite(): BelongsTo
    {
        return $this->belongsTo(
            Place::class,
            'construction_site_id',
        );
    }
}
