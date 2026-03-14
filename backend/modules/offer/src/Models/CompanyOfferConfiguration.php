<?php

namespace Modules\Offer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyOfferConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'terms_and_conditions',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo($this->getCompanyModelClass());
    }

    protected function getCompanyModelClass(): string
    {
        return config('offer.models.company', \App\Models\Company::class);
    }
}
