<?php

namespace App\Transformers\Addressbook;

use App\Transformers\PlaceTransformer;
use App\Transformers\Transformer;
use Illuminate\Support\Str;
use Packages\Urn\UrnManager;
use Sowidu\SharedData\SharedData;

/**
 * @property-read \App\Models\Addressbook $resource
 */
class AddressbookTransformer extends Transformer
{
    public function toArray($request)
    {
        if ($this->resource) {
            return $this->buildAddressbookFields([
                'id' => $this->resource->id,
                'team_id' => $this->resource->team_id,
                'urn' => UrnManager::generate($this->resource),
                'user_id' => $this->resource->user_id,
                'source_model' => implode(':', [$this->resource->model_type, $this->resource->model_id]),
                'column_values' => [
                    'name' => $this->resource->name,
                    'photo' => $this->resource?->photo ?? (new SharedData)->get('defaults.avatars.user'),
                    'email' => $this->resource->email,
                    'phone' => $this->resource->phone,

                ],
            ]);
        }

        return [];
    }

    protected function buildAddressbookFields(array $attributes)
    {
        $this->resource->loadMissing('source', 'currentPlace');
        if (
            $this->resource->isOrganization()
            || $this->resource->isForeignOrganization()
        ) {
            $attributes['column_values']['legalform'] = $this->resource->legalform;
            $attributes['column_values']['legal_form'] = $this->resource->legalform;
            $attributes['column_values']['institution_type'] = $this->resource->institution_type;
        }
        if ($this->resource->isPerson()) {
            $attributes['column_values']['first_name'] = $this->resource->details['first_name'] ?? $this->resource->source->first_name ?? null;
            $attributes['column_values']['last_name'] = $this->resource->details['last_name'] ?? $this->resource->source->last_name ?? null;
        }
        $attributes['current_place'] = $this->resource->currentPlace;

        return $attributes;
    }

    public function withOrganizationMembership($organizationMembership)
    {
        $membership = $organizationMembership->organization;

        return $this->state(function ($attributes) use ($membership) {
            $attributes['column_values']['member'] = [
                'addressbook_organization_id' => $membership->addressbook_organization_id,
                'addressbook_member_id' => $membership->addressbook_member_id,
                'position' => $membership->position,
            ];

            return $attributes;
        });
    }

    public function withAddress()
    {
        return $this->state(function ($attributes) {

            $attributes['column_values']['address'] = (new PlaceTransformer($this->currentPlace ?? null))->withShortFullAddress()->resolve();

            return $attributes;
        });
    }

    public function withOrganizationMembersCount()
    {
        return $this->state(function ($attributes) {
            return [
                'members_count' => $this->resource->organization_members_count,
            ];
        });
    }

    public function withOrganizationMember($memberAddressbook)
    {
        $membership = $memberAddressbook->organizationMember;

        return $this->state(function ($attributes) use ($membership) {
            $attributes['column_values']['member'] = [
                'addressbook_organization_id' => $membership->addressbook_organization_id,
                'addressbook_member_id' => $membership->addressbook_member_id,
                'position' => $membership->position,
            ];

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

    public function withCareOfs(): AddressbookTransformer
    {
        $this->resource?->loadMissing('organizationMembers');

        return $this->state(fn ($attributes) => ([
            'careOfs' => $this->resource->organizationMembers->whereNotNull('current_place_id')->map(fn ($addressbook) => (new AddressbookTransformer($addressbook))->resolve()),
        ]));
    }
}
