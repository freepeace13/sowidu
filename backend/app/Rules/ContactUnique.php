<?php

namespace App\Rules;

use App\Transformers\Contact\CompanyTransformer;
use App\Transformers\Contact\EmployeeTransformer;
use App\Transformers\Contact\UserTransformer;
use Auth;
use Illuminate\Contracts\Validation\Rule;

class ContactUnique implements Rule
{
    protected $typeField;

    protected $request;

    protected $value;

    public function __construct(string $typeField)
    {
        $this->typeField = $typeField;
        $this->request = request();
    }

    public function passes($attribute, $value)
    {
        $this->value = $value;

        return !$this->getQuery()->exists();
    }

    public function message()
    {
        $contact = $this->getContact();

        return $contact['full_name'] . ' is already exist in your addressbook.';
    }

    private function getQuery()
    {
        return Auth::user()
            ->contacts()
            ->where('contactable_id', $this->value)
            ->where('contactable_type', $this->getType());
    }

    private function getContact()
    {
        $contact = $this->getQuery()->first()->contactable;

        if ($contact instanceof \App\Models\User) {
            return UserTransformer::transform($contact);
        } elseif ($contact instanceof \App\Models\Company) {
            return CompanyTransformer::transform($contact);
        }

        return EmployeeTransformer::transform($contact);

    }

    private function getType()
    {
        $type = $this->request->get($this->typeField);

        return 'App\\Models\\' . ucfirst($type);
    }
}
