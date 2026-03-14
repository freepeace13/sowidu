<?php

namespace Packages\Addressable\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function model()
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }
}
