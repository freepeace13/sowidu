<?php

namespace App\Rules;

use App\Support\Facades\Impersonate;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Model $model
 */
class OwnedByCompany implements InvokableRule
{
    public function __construct(
        protected $model,
        protected string $companyColumnName = 'company_id',
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        // Validate value is owned by company
        $result = resolve($this->model)
            ->where($this->companyColumnName, Impersonate::tenant()?->id)
            ->where('id', $value)
            ->first();

        if (!$result) {
            $fail(trans('validation.company_not_owner'));
        }
    }
}
