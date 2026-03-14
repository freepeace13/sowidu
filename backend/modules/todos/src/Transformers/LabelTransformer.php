<?php

namespace Modules\Todos\Transformers;

class LabelTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'color' => $this['color'],
            'isDefault' => $this['isDefault'],
        ];
    }
}
