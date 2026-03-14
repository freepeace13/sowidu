<?php

namespace Modules\Offer\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OfferDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'deductible_id',
        'deductible_type',
    ];

    public function scopeIsDeductible(Builder $query, Model $toBeDeduction): Builder
    {
        $deductionManualClass = $this->getDeductionManualClass();

        return $query->whereHasMorph(
            'deductible',
            [$deductionManualClass],
            fn ($query) => $query->where('id', $toBeDeduction->id),
        );
    }

    public function scopeManual(Builder $query): Builder
    {
        return $query->whereMorphedTo('deductible', $this->getDeductionManualClass());
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function deductible(): MorphTo
    {
        return $this->morphTo();
    }

    protected function getDeductionManualClass(): string
    {
        return config('offer.models.deduction_manual', \App\Models\DeductionManual::class);
    }
}
