<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseNumber extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_number',
    ];

    public function label()
    {
        return $this->house_number;
    }
}
