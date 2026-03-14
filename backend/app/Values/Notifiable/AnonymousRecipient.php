<?php

namespace App\Values\Notifiable;

abstract class AnonymousRecipient
{
    public $value;

    public static $driver;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getDriver()
    {
        return static::$driver;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function make($value)
    {
        return new static($value);
    }
}
