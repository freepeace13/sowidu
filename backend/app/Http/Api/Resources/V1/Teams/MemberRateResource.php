<?php

namespace App\Http\Api\Resources\V1\Teams;

use Packages\RestApi\Resources\JsonResource;

class MemberRateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'memberId' => $this->employee_id,
            'teamId' => $this->employee->company_id,
            'rate' => $this->rate,
            'currency' => $this->currency,
        ];
    }
}
