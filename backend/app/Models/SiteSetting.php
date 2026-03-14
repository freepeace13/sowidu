<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteSetting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'terms_conditions',
    ];

    public $translatable = [
        'terms_conditions',
    ];

    public static function app()
    {
        return static::find(config('app.id'));
    }
}
