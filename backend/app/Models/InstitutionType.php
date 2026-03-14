<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionType extends Model
{
    /**
     * Disabling timestamp
     */
    public $timestamps = false;

    /**
     * [protected description]
     *
     * @var [type]
     */
    protected $fillable = [
        'type', 'abbreviation',
    ];
}
