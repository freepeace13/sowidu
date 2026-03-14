<?php

namespace Modules\Catalog\Actions\Concerns;

use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Modules\Catalog\Contracts\External\MediaManagerContract;

trait ValidatesCatalogItems
{
    public function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'name' => 'required|string',
            'type' => 'required|string',
            'media' => [
                // 'required',
                'exists:media_files,id',
            ],
            'vendor_id' => ['nullable', 'string'],
            'internal_id' => ['nullable', 'string'],
            'manufacture_id' => ['required', 'string'],
            'unit' => [
                'required',
                'integer',
                'exists:catalog_item_units,id',
            ],
            'purchasing_price' => 'nullable',
            'selling_price' => 'required|numeric',
            'description' => 'required|string',
        ])->validate();
    }

    public function getMediaFromInput(Company $company, $input)
    {
        /** @var MediaManagerContract $mediaManager */
        $mediaManager = $this->mediaManager ?? app(MediaManagerContract::class);

        return $mediaManager->findForCompany($company, (int) $input);
    }
}
