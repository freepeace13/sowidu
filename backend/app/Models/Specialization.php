<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    /**
     * Disabling timestamp.
     */
    public $timestamps = false;

    /**
     * [protected description].
     *
     * @var [type]
     */
    protected $fillable = [
        'title', 'description',
    ];

    /**
     * Local scope the get the record with matching the title given.
     *
     * @param [type] $query
     * @param [type] $position
     * @return void
     */
    public function scopeTitle($query, $position)
    {
        return $query->where('title', $position)->first();
    }

    public function scopeEmployee($query)
    {
        return $query->where('title', 'Employee');
    }
}
