<?php

namespace App\Http\Api\Resources\V1\Addressbook;

use App\Transformers\PlaceTransformer;
use Illuminate\Support\Str;
use Packages\RestApi\Resources\JsonResource;
use Packages\Urn\UrnManager;

/**
 * @property \App\Models\Addressbook $resource
 */
class AddressbookResource extends JsonResource
{
    public function toArray($request)
    {
        $this->resource;

        return [
            'id' => $this->resource->id,
            'teamId' => $this->resource->team_id,
            'urn' => UrnManager::generate($this->resource),
            'userId' => $this->resource->user_id,
            'sourceModel' => implode(':', [$this->resource->model_type, $this->resource->model_id]),
            'columnValues' => $this->buildColumnValues(),
        ];
    }

    protected function buildColumnValues(): array
    {
        $attributes = [
            'name' => $this->resource->name,
            'photo' => $this->resource->photo,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
        ];

        if (
            $this->resource->isOrganization()
            || $this->resource->isForeignOrganization()
        ) {
            $attributes['legalform'] = $this->resource->legalform;
            $attributes['institutionType'] = $this->resource->institution_type;
        }

        return $attributes;
    }

    public function withOrganizationMembership($organizationMembership)
    {
        $membership = $organizationMembership->organization;

        return $this->state(function ($attributes) use ($membership) {
            $attributes['columnValues']['member'] = [
                'addressbookOrganizationId' => $membership->addressbook_organization_id,
                'addressbookMemberId' => $membership->addressbook_member_id,
                'position' => $membership->position,
            ];

            return $attributes;
        });
    }

    public function withAddress()
    {
        return $this->state(function ($attributes) {
            $attributes['columnValues']['address'] = (new PlaceTransformer($this->resource->currentPlace))->resolve();

            return $attributes;
        });
    }

    public function withModelType()
    {
        return $this->state(function ($attributes) {
            return [
                'type' => Str::singular(
                    $this->resource->type ?? $this->resource->foreign_model_type,
                ) == 'user' ? 'person' : 'organization',
            ];
        });
    }
}
