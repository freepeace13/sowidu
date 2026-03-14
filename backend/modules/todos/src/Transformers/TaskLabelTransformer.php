<?php

namespace Modules\Todos\Transformers;

class TaskLabelTransformer extends Transformer
{
    public function __construct($resource)
    {
        parent::__construct(...func_get_args());
    }

    public function toArray($request)
    {
        return [
            'task_id' => $this['task_id'],
            'label_id' => $this['label_id'],
        ];
    }

    public function withLabel()
    {
        return $this->state(function () {
            return [
                'label' => (new LabelTransformer($this->label))->resolve(),
            ];
        });
    }
}
