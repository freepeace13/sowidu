<?php

namespace App\Http\Requests\Accounts;

use App\Enums\Gender;
use App\Support\Facades\Impersonate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'avatar' => ['nullable'],
            // 'street' => ['nullable', 'string'],
            // 'house_number' => ['nullable', 'string'],
            // 'zipcode' => ['nullable', 'string'],
            // 'city' => ['nullable', 'string'],
            // 'country' => ['required', 'in:' . join(',', $countries)],
            // 'state' => ['required_with:country', 'in:' . join(',', $states)]
        ];

        if (!Impersonate::isImpersonating()) {
            $rules = array_merge($rules, [
                'first_name' => ['required', 'string'],
                'last_name' => ['required', 'string'],
                'birthdate' => ['required', 'date'],
                'gender' => ['nullable', 'in:' . implode(',', array_values(Gender::getConstants()))],
            ]);
        } else {
            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    (Impersonate::tenant()->name !== $this->name) ? 'unique:companies,name' : '',
                ],
            ]);
        }

        return $rules;
    }
}
