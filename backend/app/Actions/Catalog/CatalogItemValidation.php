<?php

namespace App\Actions\Catalog;

use App\Models\Company;
use App\Services\MediaFileService;
use Illuminate\Support\Facades\Validator;

trait CatalogItemValidation
{
    public function validate(array $inputs): array
    {
        return Validator::make($inputs, [
            'name' => 'required|string',
            'type' => 'required|string',
            'media' => [
                'nullable',
                'exists:media_files,id',
            ],
            'vendor_id' => 'nullable',
            'manufacture_id' => 'required|string',
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
        return MediaFileService::makeForCompany($company)->findOrFail($input);
    }
}
