<?php

namespace App\Models;

use App\Enums\PaymentForm;
use App\Models\Relations\CanBeInvoiceItem;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class WorkLog extends Model
{
    use CanBeInvoiceItem;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'started_at',
        'ended_at',
        'duration_in_seconds',
        'is_shared',
        'notes',
        'payment_form',
        'document_number',
        'document_date',
        'event',
        'is_paid',
    ];

    protected $with = [
        'user',
    ];

    protected $casts = [
        'started_at' => 'datetime:Y-m-d H:i:s',
        'ended_at' => 'datetime:Y-m-d H:i:s',
        'is_shared' => 'boolean',
        'is_paid' => 'boolean',
        'payment_form' => PaymentForm::class,
    ];

    /** @see causer() */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** User work_log or owned by user */
    public function causer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** User who made/created this work_log */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reports()
    {
        return $this->hasMany(ActivityLogReport::class);
    }

    public function isCurrentlyWorking(): bool
    {
        return blank($this->ended_at);
    }

    public function isClosed(): bool
    {
        return $this->isCurrentlyWorking() === false;
    }

    /** WorkLog has ended_at or it is NOT in-progress */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull('ended_at');
    }

    public function getDurationForHumanAttribute(): ?string
    {
        if (!$this->duration_in_seconds || $this->duration_in_seconds == 0) {
            return null;
        }

        // Convert seconds to CarbonInterval
        $interval = CarbonInterval::seconds($this->duration_in_seconds)->cascade();

        // Calculate total hours to prevent day conversion
        $totalHours = floor($this->duration_in_seconds / 3600);
        $minutes = floor(($this->duration_in_seconds % 3600) / 60);

        // Create a new interval with total hours and minutes
        $interval = CarbonInterval::hours($totalHours)->minutes($minutes);

        // Format as human-readable with hours and minutes only
        return $interval->forHumans([
            'join' => ' and ',       // Join hours and minutes with "and"
            'parts' => 2,            // Limit to 2 parts: hours and minutes
            'minimumUnit' => 'minute', // Only show up to minutes
        ]);
    }

    public function item(): MorphOne
    {
        return $this->morphOne(InvoiceItem::class, 'item');
    }
}
