<?php

namespace Modules\Offer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Offer\Enums\OfferActionType;

class OfferHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'author_id',
        'action_type',
    ];

    protected $casts = [
        'action_type' => OfferActionType::class,
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo($this->getUserModelClass(), 'author_id');
    }

    protected function getUserModelClass(): string
    {
        return config('offer.models.user', \App\Models\User::class);
    }
}
