<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class MorphUnique implements Rule
{
    protected $model;

    protected $morphs;

    protected $typeField;

    public function __construct(Model $model, $morphs, ...$params)
    {
        $this->model = $model;
        $this->morphs = $morphs;
        $this->typeField = $this->setTypeField($params);
    }

    public function passes($attribute, $value)
    {
        $record = $this->getQuery($value);

        return !$record->exists();
    }

    public function message()
    {
        return 'Polymorphic ' . $this->morphs . ' values already exists.';
    }

    private function getQuery($value)
    {
        $idCol = $this->morphs . '_id';
        $typeCol = $this->morphs . '_type';
        $typeValue = $this->getTypeValue();

        return $this->model::where($typeCol, $typeValue)->where($idCol, $value);
    }

    private function getTypeValue()
    {
        $value = request()->get($this->typeField);

        return 'App\\Models\\' . ucfirst($value);
    }

    private function setTypeField(array $params)
    {
        return isset($params[0])
            ? $params[0]
            : $this->getModelMorphType();
    }

    private function getModelMorphType()
    {
        return $this->model->{$this->morphs}()->getMorphType();
    }
}
