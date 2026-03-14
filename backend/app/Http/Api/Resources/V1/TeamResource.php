<?php

namespace App\Http\Api\Resources\V1;

use App\Models\Place;
use App\Models\User;
use App\Transformers\CompanyTransformer;
use Packages\RestApi\Resources\JsonResource;
use Packages\Urn\UrnManager;

/**
 * @property-read \App\Models\Company $resource
 *
 * @mixin \App\Transformers\CompanyTransformer
 */
class TeamResource extends JsonResource
{
    protected $transformer = CompanyTransformer::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'urn' => UrnManager::generate($this->resource),
            'photo' => get_company_avatar_url($this->resource),
            'legalForm' => $this->when($this->legalForm, fn () => [
                'id' => $this->legalForm->id,
                'name' => $this->legalForm->legal_form,
            ]),
            'institutionType' => $this->when($this->institutionType, fn () => [
                'id' => $this->institutionType->id,
                'name' => $this->institutionType->type,
            ]),
        ];
    }

    public function withCurrentAddress(?Place $address)
    {
        return $this->state(fn ($attributes) => [
            'address' => (new PlaceResource($address))->withShortAddress(),
        ]);
    }

    public function withTaxSettings()
    {
        return $this->state(fn () => [
            'vatIdentificationNumber' => $this->vat_identification_number,
            'taxNumber' => $this->tax_number,
        ]);
    }

    public function withInvoiceDefaults(): self
    {
        return $this->state(fn () => [
            'invoiceDefaults' => array_merge([
                'iban' => $this->resource->iban,
                'bic' => $this->resource->bic,
                'bankName' => $this->resource->bank_name,
                'managingDirector' => null,
                'currency' => [
                    'name' => $this->resource->currency,
                    'symbol' => currency_symbol($this->resource->currency),
                ],
            ], $this->settings()->invoiceDefaults()->all()),
        ]);
    }

    public function withMembershipId(User $user)
    {
        return $this->state(function ($data) use ($user) {
            return [
                'membershipId' => $user->teamMembership($this->resource)?->id,
            ];
        });
    }

    public function withRoles()
    {
        return $this->state(function ($data) {
            return [
                'roles' => $this->resource->roles->pluck('name')->all(),
            ];
        });
    }

    public function withMembers()
    {
        return $this->state(function ($attributes) {
            return [
                'members' => UserResource::collection(
                    $this->users,
                    function ($resource) {
                        $resource
                            ->withRoles($this->resource)
                            ->withPermissions($this->resource);
                    },
                ),
            ];
        });
    }
}
