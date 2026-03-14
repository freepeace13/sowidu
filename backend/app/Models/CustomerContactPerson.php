<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContactPerson extends Model
{
    protected $table = 'customer_contact_persons';

    protected $fillable = [
        'customer_id',
        'name',
        'phone',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
