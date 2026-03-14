<?php

namespace Packages\RestApi;

use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class RestApiAction
{
    protected $rules;

    protected $messages;

    protected $attributes;

    public function validate(array $data, ?string $errorBag = null)
    {
        return $this->createValidator($data)->validateWithBag(
            $this->getErrorBagName($errorBag),
        );
    }

    public function getValidationRules(): array
    {
        return $this->rules ?: [];
    }

    public function getValidationMessages(): array
    {
        return $this->messages ?: [];
    }

    public function getValidationCustomAttributes(): array
    {
        return $this->attributes ?: [];
    }

    public function getErrorBagName($errorBag = null)
    {
        return $errorBag ?: Str::camel(class_basename($this));
    }

    protected function createValidator(array $data)
    {
        return app(Validator::class)->make(
            $data,
            $this->getValidationRules(),
            $this->getValidationMessages(),
            $this->getValidationCustomAttributes(),
        );
    }

    protected function throwValidationError(array $messages, $errorBag = null)
    {
        throw ValidationException::withMessages($messages)->errorBag(
            $this->getErrorBagName($errorBag),
        );
    }
}
