<?php

namespace App\Contracts;

interface Storage
{
    public function has($key);

    public function set($key, $value);

    public function get($key, $default = null);

    public function forget($key);
}
