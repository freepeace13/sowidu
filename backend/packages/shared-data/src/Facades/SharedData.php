<?php

namespace Sowidu\SharedData\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sowidu\SharedData\SharedData
 *
 * @method static \Sowidu\SharedData\SharedData put(mixed $key, mixed $value = null)
 * @method static mixed get(mixed $key = null)
 * @method static string toJson(int $options = 0)
 * @method static string render()
 */
class SharedData extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Sowidu\SharedData\SharedData::class;
    }
}
