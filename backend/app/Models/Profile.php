<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Packages\Avatarable\Traits\HasAvatar;

class Profile extends Model
{
    use HasAvatar;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function model()
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }
}
