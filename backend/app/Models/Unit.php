<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @TODO - This model is deprecated and will be removed in the future.
 *
 * @deprecated - This model is deprecated and will be removed in the future.
 * @see \App\Models\CatalogItemUnit - this is the new model to use.
 */
class Unit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
