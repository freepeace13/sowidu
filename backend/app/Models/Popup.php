<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    public $timestamps = false;

    protected $dates = [
        'skipped_at',
    ];

    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'type',
        'skipped_at',
    ];

    public function owner()
    {
        return $this->morphTo('ownerable');
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeTypes($query, array $types)
    {
        return $query->whereIn('type', $types);
    }
}
