<?php

namespace App\Transformers;

use App\Models\Place;
use App\Models\User;

/**
 * @property-read \App\Models\Company $resource
 */
class CompanyTransformer extends Transformer
{
    use Traits\WithUrnAttribute;

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'uuid' => $this->resource->uuid ?? null,
            'name' => $this->resource->name,
            'username' => $this->resource->username ?? null,
            'photo' => get_company_avatar_url($this->resource),
            'currency' => [
                'name' => $this->resource->currency,
                'symbol' => currency_symbol($this->resource->currency),
            ],
        ];
    }

    public function withColumnValues()
    {
        return $this->state(fn ($attr) => [
            'column_values' => [
                'name' => $this->resource->name,
                'photo' => get_company_avatar_url($this->resource),
                // 'email' => $biller->settings()
                // ->invoiceDefaults()
                // ->get('company_email'),
                // 'phone' => $biller->settings()
                // ->invoiceDefaults()
                // ->get('company_phone'),

            ],
        ]);
    }

    public function withCurrentUserEmployment()
    {
        return $this->state(fn ($attributes) => [
            'user_employment' => [
                'role' => $this->resource->currentUserEmployment?->role,
            ],
        ]);
    }

    public function withCurrentAddress(?Place $address)
    {
        return $this->state(fn ($attributes) => [
            'address' => (new PlaceTransformer($address))
                ->withShortFullAddress()
                ->resolve(),
        ]);
    }

    public function withType()
    {
        $this->resource->loadMissing(['legalForm', 'institutionType']);

        return $this->state(fn ($attributes) => [
            'legal_form' => $this->resource->legalForm?->toArray(),
            'institution_type' => $this->resource->institutionType?->toArray(),
        ]);
    }

    public function withCompanyOwnerDetails(User $user)
    {
        return $this->state(fn () => [
            'email' => $user->email,
        ]);
    }

    public function withTaxSettings()
    {
        return $this->state(fn () => [
            'vat_identification_number' => $this->resource->vat_identification_number,
            'tax_number' => $this->resource->tax_number,
        ]);
    }

    public function withInvoiceDefaults(): self
    {
        return $this->state(fn () => [
            'invoice_defaults' => array_merge([
                'iban' => $this->resource->iban,
                'bic' => $this->resource->bic,
                'bank_name' => $this->resource->bank_name,
                'managing_director' => null,
                'currency' => [
                    'name' => $this->resource->currency,
                    'symbol' => currency_symbol($this->resource->currency),
                ],
            ], $this->resource->settings()
                ->invoiceDefaults()
                ->all()),
        ]);
    }
}
