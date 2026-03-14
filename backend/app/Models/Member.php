<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'memberables';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'member_type',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['member'];

    /**
     * @return \Illuminate\Database\Eloquent\Relation\MorphTo
     */
    public function member()
    {
        return $this->morphTo();
    }
}
