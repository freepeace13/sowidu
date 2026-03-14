<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate',
        'currency',
    ];

    protected $with = ['employee'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
