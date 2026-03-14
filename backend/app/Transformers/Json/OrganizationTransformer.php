<?php

namespace App\Transformers\Json;

use App\Transformers\Traits\WithPlaceAttribute;
use Packages\Urn\UrnManager;

class OrganizationTransformer extends JsonTransformer
{
    use WithPlaceAttribute;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'urn' => UrnManager::generate($this->resource),
            'name' => $this->name,
            'photo' => get_company_avatar_url($this->resource),
            'legalform' => $this->loadMissing(['legalForm'])->legalForm?->legal_form,
            'institution_type' => $this->loadMissing(['institutionType'])->institutionType?->type,
        ];
    }

    public function withAddress()
    {
        return $this->withPlace('address');
    }
}
