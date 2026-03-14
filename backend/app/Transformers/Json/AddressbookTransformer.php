<?php

namespace App\Transformers\Json;

use App\Transformers\Traits\WithPlaceAttribute;
use Packages\Urn\UrnManager;

/**
 * @property-read \App\Models\Addressbook $resource
 */
class AddressbookTransformer extends JsonTransformer
{
    use WithPlaceAttribute;

    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'urn' => UrnManager::generate($this->resource),
            'name' => $this->name,
            'photo' => $this->photo,
        ];

        if ($this->resource->isPerson() || $this->resource->isForeignPerson()) {
            $data = $this->appendsPersonAttributes($data);
        }

        if (
            $this->resource->isOrganization()
            || $this->resource->isForeignOrganization()
        ) {
            $data = $this->appendsOrganizationAttributes($data);
        }

        return $data;
    }

    protected function appendsPersonAttributes(array $data)
    {
        return $data + [
            'phone' => $this->phone,
            'email' => $this->email,
            'first_name' => $this->resource->details['first_name'] ?? $this->name,
            'last_name' => $this->resource->details['last_name'] ?? '',
        ];
    }

    protected function appendsOrganizationAttributes(array $data)
    {
        return $data + [
            'legalform' => $this->legalform,
            'institution_type' => $this->institution_type,
            'phone' => $this->phone,
        ];
    }

    public function withAddress()
    {
        return $this->withPlace('address');
    }

    public function withOrganization(?int $organizationId = null)
    {
        if (!$organizationId) {
            return $this->state(fn ($attributes) => $attributes);
        }

        return $this->state(function ($attributes) {
            $membership = $this->resource->organizations->first()->organization;

            return [
                'position' => $membership->position,
            ];
        });
    }
}
