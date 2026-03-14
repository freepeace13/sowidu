<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyInvitation extends Model
{
    protected $table = 'company_invites';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'role',
        'company_id',
        'email',
        'note',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'declined' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function isPending(): bool
    {
        return !$this->accepted_at && !$this->declined;
    }

    public function accept()
    {
        $this->accepted_at = now();
        $this->save();
    }

    public function decline()
    {
        $this->declined = true;
        $this->save();
    }

    public function revoke()
    {
        $this->revoked = true;
        $this->save();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopePending(Builder $query)
    {
        $query->where('revoked', 0)
            ->where('declined', 0)
            ->whereNull('accepted_at');
    }

    public function scopeFailed(Builder $query)
    {
        $query->where('revoked', 0)
            ->where('declined', 1)
            ->whereNull('accepted_at');
    }

    public function scopeOnCompany(Builder $query, Company $company)
    {
        return $query->whereRelation('company', 'id', $company->id);
    }
}
