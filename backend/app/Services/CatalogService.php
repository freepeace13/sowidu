<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\CatalogItemType;
use App\Models\CatalogItemUnit;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Packages\MediaLibrary\MediaCollections\Models\Media;

class CatalogService
{
    public function __construct(protected User $user, protected Company $company) {}

    public static function make(User $user, Company $company)
    {
        return new static($user, $company);
    }

    public function allItemTypes()
    {
        return CatalogItemType::query()
            ->where('company_id', $this->company->getKey())
            ->get();
    }

    public function updateVendorAndInternalIds(CatalogItem $catalogItem)
    {
        $catalogItem->update([
            'internal_id' => $this->generateInternalId($catalogItem->id),
            'vendor_id' => $this->generateVendorId(),
        ]);
    }

    public function createItem(
        CatalogItemType $catalogItemType,
        array $payload,
        ?Media $media = null,
    ): ?CatalogItem {
        $catalogItem = DB::transaction(
            function () use ($payload, $catalogItemType, $media) {
                // Add unit_name from `unit`
                $unit = data_get($payload, 'unit');
                if (filled($unit)) {
                    $unit = CatalogItemUnit::find($unit);

                    if (blank($unit)) {
                        throw new \Exception('Unit not found');
                    }

                    data_set($payload, 'unit_name', $unit->name);
                }

                $catalogItem = new CatalogItem(
                    Arr::only($payload, [
                        'name',
                        'internal_id',
                        'vendor_id',
                        'unit',
                        'unit_name',
                        'manufacture_id',
                        'purchasing_price',
                        'selling_price',
                        'description',
                    ]),
                );

                $catalogItem->type()
                    ->associate($catalogItemType);
                $catalogItem->author()
                    ->associate($this->user);
                $catalogItem->company()
                    ->associate($this->company);
                if ($media) {
                    $catalogItem->media()
                        ->associate($media);
                }
                $catalogItem->save();

                return $catalogItem;
            },
            5,
        );

        if (!$catalogItem) {
            return null;
        }

        // Fill in some fields
        return tap($catalogItem)->update([
            'vendor_id' => $this->generateVendorId(),
            'internal_id' => $this->generateInternalId($catalogItem->id),
        ]);
    }

    protected function generateVendorId(): string
    {
        $companyInitial = CompanyService::make($this->company)->getCompanyInitial();

        return "$companyInitial-" . crc32($this->company->getKey());
    }

    protected function generateInternalId(int $catalogItemId): string
    {
        return 'SW-' . crc32($catalogItemId);
    }

    public function productOwned(CatalogItem $item): bool
    {
        return $item->company->is($this->company);
    }
}
