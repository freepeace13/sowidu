<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactNumber extends Model
{
    /**
     * [protected description]
     *
     * @var [type]
     */
    protected $fillable = [
        'ownerable_id', 'ownerable_type', 'fax', 'mobile', 'landline',
    ];

    /**
     * [ownerable description]
     *
     * @return [type] [description]
     */
    public function ownerable()
    {
        return $this->morphTo();
    }
}
