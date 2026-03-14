<?php

declare(strict_types=1);

namespace Modules\WorkLogs\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\WorkLogs\Database\Factories\WorkLogFactory;
use Modules\WorkLogs\Enums\PaymentForm;
use Modules\WorkLogs\Models\Concerns\CanBeInvoiceItem;
use Modules\WorkLogs\Models\Concerns\HasExternalRelationships;

class WorkLog extends Model
{
    use CanBeInvoiceItem;
    use HasExternalRelationships;
    use HasFactory;

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

    protected static function newFactory()
    {
        return WorkLogFactory::new();
    }

    public function isCurrentlyWorking(): bool
    {
        return blank($this->ended_at);
    }

    public function isClosed(): bool
    {
        return $this->isCurrentlyWorking() === false;
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull('ended_at');
    }

    public function getDurationForHumanAttribute(): ?string
    {
        if (!$this->duration_in_seconds || $this->duration_in_seconds == 0) {
            return null;
        }

        $interval = CarbonInterval::seconds($this->duration_in_seconds)->cascade();

        $totalHours = floor($this->duration_in_seconds / 3600);
        $minutes = floor(($this->duration_in_seconds % 3600) / 60);

        $interval = CarbonInterval::hours($totalHours)->minutes($minutes);

        return $interval->forHumans([
            'join' => ' and ',
            'parts' => 2,
            'minimumUnit' => 'minute',
        ]);
    }
}
