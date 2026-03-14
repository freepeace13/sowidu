<?php

namespace Modules\Todos\Support;

use Illuminate\Contracts\Support\Arrayable;

class BoardGroup implements Arrayable
{
    public $name;

    public $color;

    public $order;

    public $isDefault;

    public function __construct($name, $color, int $order, bool $isDefault)
    {
        $this->name = $name;
        $this->color = $color;
        $this->order = $order;
        $this->isDefault = $isDefault;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'color' => $this->color,
            'order' => $this->order,
            'default' => $this->isDefault,
        ];
    }
}
