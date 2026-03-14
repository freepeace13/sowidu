<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'deductible_id',
        'deductible_type',
    ];

    public function scopeIsDeductible(Builder $query, $toBeDeduction): Builder
    {
        return $query->whereHasMorph(
            'deductible',
            [Invoice::class, DeductionManual::class],
            fn ($query) => $query->where('id', $toBeDeduction->id),
        );
    }

    public function scopeOnlyInvoice(Builder $query): Builder
    {
        return $query->whereMorphedTo('deductible', Invoice::class);
    }

    public function scopeManual(Builder $query): Builder
    {
        return $query->whereMorphedTo('deductible', DeductionManual::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return Invoice|DeductionManual|\Illuminate\Database\Eloquent\Relations\MorphTo */
    public function deductible()
    {
        return $this->morphTo();
    }
}
