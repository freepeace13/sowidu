<?php

namespace App\Transformers\Json;

use App\Transformers\Traits\WithPlaceAttribute;
use Packages\Urn\UrnManager;

class PersonTransformer extends JsonTransformer
{
    use WithPlaceAttribute;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'urn' => UrnManager::generate($this->resource),
            'name' => $this->fullName,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->mobile,
            'photo' => get_user_avatar_url($this->resource),
        ];
    }

    public function withAddress()
    {
        return $this->withPlace('address');
    }
}
