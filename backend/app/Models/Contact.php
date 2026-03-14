<?php

namespace App\Models;

use App\Contracts\Contactable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Packages\Addressable\Traits\HasAddresses;
use Packages\Avatarable\Traits\HasAvatar;

class Contact extends Model
{
    use Filterable,
        HasAddresses,
        HasAvatar,
        Relations\Searchable;

    protected $searchable = [
        'relations' => ['contactable'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'contactable_id',
        'contactable_type',
        'preference_data',
        'invitation_id',
        'pd_outdated',
        'deleted_at',
        'confirmed',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'preference_data' => 'array',
    ];

    /**
     * Determine the contact is already confirmed.
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return (bool) $this->confirmed;
    }

    /**
     * Scope a query that only include contacts that match the specified
     * contactable
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @param  App\Contracts\Contactable  $contactable
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhich(Builder $query, Contactable $contactable): Builder
    {
        return $query->where([
            'contactable_id' => $contactable->id,
            'contactable_type' => $contactable->getMorphClass(),
        ]);
    }

    /**
     * Scope a query that only include contacts that contactable is company
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompanies(Builder $query): Builder
    {
        return $query->where('contactable_type', (new Company)->getMorphClass());
    }

    /**
     * Scope a query that only include contacts that contactable is `employee`
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmployees(Builder $query): Builder
    {
        return $query->where('contactable_type', (new Employee)->getMorphClass());
    }

    /**
     * Scope a query that only include contacts that contactable is `private`
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsers(Builder $query): Builder
    {
        return $query->where('contactable_type', (new User)->getMorphClass());
    }

    /**
     * Scope a query that only include outdated contacts
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOutdated(Builder $query): Builder
    {
        return $query->where('pd_outdated', 1);
    }

    /**
     * Scope a query that only include undeleted contacts
     *
     * @param  Illuminate\Database\Eloquent\Builder  $query
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUndeleted(Builder $query): Builder
    {
        return $query->where(['deleted_at' => null]);
    }

    /**
     * Get the contact owner
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function ownerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the contact contactable
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
