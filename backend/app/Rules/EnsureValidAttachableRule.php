<?php

namespace App\Rules;

use App\Contracts\Attachment\Attachable;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EnsureValidAttachableRule implements Rule
{
    /**
     * The validation rule message.
     *
     * @var string
     */
    protected $reason;

    /**
     * The error message templates.
     *
     * @var array
     */
    protected $templates = [
        'invalid_value' => 'The :attribute value type :value of id :value is invalid.',
        'missing_attrs' => "The :attribute values 'doc_type' and 'id' attributes are missing.",
    ];

    /**
     * Determine given value of doc_type and id are exists and valid.
     *
     * @param  array  $value
     * @return bool
     */
    protected function checkValidValue($value)
    {
        $repository = app(\App\Repositories\AttachmentRepository::class);
        $modelInstance = $repository->resolve($value);

        if (!$modelInstance instanceof Attachable) {
            $template = $this->templates['invalid_value'];

            $this->reason = Str::replaceArray(':value', [
                $value['doc_type'], $value['id'],
            ], $template);

            return false;
        }

        return true;
    }

    /**
     * Determine given attribute value constains valid attributes.
     *
     * @param  array  $value
     * @return bool
     */
    protected function checkValidAttrs($value)
    {
        if (!Arr::has($value, ['doc_type', 'id'])) {
            $this->reason = $this->templates['missing_attrs'];

            return false;
        }

        return true;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkValidAttrs($value)
            && $this->checkValidValue($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->reason;
    }
}
