<?php

namespace App\Models;

use App\Actions\Category\RemoveCategoryTagOnAllMediaOwned;
use App\Models\QueryBuilders\QueryHelper;
use App\Support\Category\CategorySettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use QueryHelper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'name',
        'settings',
        'is_default',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleting(function ($category) {
            (new RemoveCategoryTagOnAllMediaOwned)->remove($category->ownerable, $category->name);
        });
    }

    public function settings(): CategorySettings
    {
        return new CategorySettings($this);
    }

    public function ownerable()
    {
        return $this->morphTo();
    }

    public function scopeOwnedBy(Builder $query, User|Company $account)
    {
        return $query->where('ownerable_id', $account->getKey())
            ->where('ownerable_type', $account->getMorphClass());
    }
}
