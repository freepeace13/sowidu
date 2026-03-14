<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalForm extends Model
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
        'legal_form', 'abbreviation',
    ];
}
