<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLogbook extends Model
{
    /**
     * Model table name
     *
     * @var string
     */
    protected $table = 'company_logbook';

    /**
     * [protected description]
     *
     * @var [type]
     */
    protected $fillable = ['user_id', 'company_id'];

    /**
     * Get the user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the company
     *
     * @return void
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
