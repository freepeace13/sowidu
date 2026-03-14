<?php

namespace Database\Transformers;

use App\Models\Employee;

class MemberablesMemberMorphsFromEmployeeId
{
    /**
     * @return array
     */
    public function __invoke(array $item, int $key)
    {
        if (isset($item['employee_id'])) {
            $item['member_id'] = $item['employee_id'];
            $item['member_type'] = model_alias(Employee::class);

            unset($item['employee_id']);
        }

        return $item;
    }
}
