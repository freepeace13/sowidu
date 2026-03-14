<?php

namespace App\Models;

use App\Models\Relations\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tax extends Model
{
    use CompanyOwned;
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class)
            ->withTimestamps();
    }

    public function scopeDefault($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_default', true);
    }
}
