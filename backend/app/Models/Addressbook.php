<?php

namespace App\Models;

use App\Events\Addressbook\AddressbookDeleted;
use App\Models\Company as Team;
use App\Models\Concerns\HasProfile;
use App\Models\Relations\Searchable;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Packages\Addressable\Traits\HasAddresses;

class Addressbook extends Model
{
    use Concerns\HasPlaces,
        Filterable,
        HasAddresses,
        HasFactory,
        HasProfile,
        Relations\HasOrders,
        Searchable,
        SoftDeletes;

    const FOREIGN_PERSON = 0;
    const FOREIGN_ORGANIZATION = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'photo',
        'email',
        'phone',
        'institution_type',
        'legalform',
        'team_id',
        'user_id',
        'model_id',
        'model_type',
        'foreign_type',
        'details',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    protected $searchable = [
        'columns' => ['name', 'email', 'team_id'],
    ];

    // TODO - Remove! additional query to eager load current place even not needed/used
    protected $with = ['currentPlace'];

    protected static function booted()
    {
        static::deleting(function (self $addressbook) {
            event(new AddressbookDeleted($addressbook));
        });

        static::forceDeleted(function (self $addressbook) {
            $addressbook->currentPlace()
                ->delete();
        });
    }

    public function getTypeAttribute()
    {
        if (blank($this->getAttribute('model_type'))) {
            return $this->attributes['foreign_type'] == self::FOREIGN_PERSON
                ? model_alias(User::class)
                : model_alias(Company::class);
        }

        return $this->attributes['model_type'];
    }

    public function isPerson()
    {
        return $this->type == (new User)->getMorphClass();
    }

    public function isOrganization()
    {
        return $this->type == (new Team)->getMorphClass();
    }

    public function source(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function organizationMembers(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'addressbook_organization_members',
            'addressbook_organization_id',
            'addressbook_member_id',
        )
            ->as('organizationMember')
            ->withPivot('position');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'addressbook_organization_members',
            'addressbook_member_id',
            'addressbook_organization_id',
        )
            ->as('organization')
            ->withPivot('position');
    }

    public function scopeOwnedByUser(Builder $query, User $user)
    {
        return $query->where('user_id', $user->getKey());
    }

    public function scopeForeign(Builder $query)
    {
        return $query->whereNull('model_id')
            ->whereNull('model_type');
    }

    public function doesHaveMember(self $member): bool
    {
        return $this->whereHas(
            'organizationMembers',
            fn ($query) => $query->where('addressbook_member_id', $member->id),
        )
            ->exists();
    }

    public function doesntHaveMember(self $member): bool
    {
        return !$this->doesHaveMember($member);
    }

    public function isOwnedByUser(User $user): bool
    {
        return $this->user->is($user);
    }

    public function isOwnedByTeam(int $teamId)
    {
        return $this->team_id === $teamId;
    }

    /**
     * Foreign related scopes, methods etc...
     */
    public function isForeign()
    {
        return blank($this->model_id) && blank($this->model_type);
    }

    public function isForeignPerson()
    {
        return $this->isForeign() && $this->foreign_type === self::FOREIGN_PERSON;
    }

    public function isForeignOrganization()
    {
        return $this->isForeign() && $this->foreign_type === self::FOREIGN_ORGANIZATION;
    }

    public function getForeignModelTypeAttribute()
    {
        return $this->attributes['foreign_model_type'] = $this->isForeignOrganization() ? 'companies' : 'users';
    }

    public function clientOrders()
    {
        return $this->hasMany(Order::class, 'client_addressbook_id', 'id');
    }

    public function contractorDeals()
    {
        return $this->hasMany(Order::class, 'contractor_addressbook_id', 'id');
    }

    /** @return HasMany|Invoice */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'biller_id');
    }

    /**
     * Get the company's legal form.
     */
    public function legalForm(): BelongsTo
    {
        return $this->belongsTo(LegalForm::class, 'legal_form_id');
    }

    /**
     * Get the company's institution type.
     */
    public function institutionType(): BelongsTo
    {
        return $this->belongsTo(InstitutionType::class, 'institution_type_id');
    }
}
