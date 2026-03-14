<?php

namespace Modules\Catalog\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Catalog\Models\CatalogItem;
use Modules\Catalog\Models\CatalogItemType;
use Modules\Catalog\Models\CatalogItemUnit;
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

                // Get attributes
                $attributes = Arr::only($payload, [
                    'name',
                    'unit',
                    'unit_name',
                    'manufacture_id',
                    'purchasing_price',
                    'selling_price',
                    'description',
                ]);

                // Generate temporary internal_id and vendor_id if not provided
                // These will be updated with the real values after save (when we have the ID)
                if (!isset($payload['internal_id']) || is_null($payload['internal_id'])) {
                    // Generate temporary ID that will be replaced after save
                    $attributes['internal_id'] = 'SW-TEMP-' . uniqid();
                } else {
                    $attributes['internal_id'] = $payload['internal_id'];
                }

                if (!isset($payload['vendor_id']) || is_null($payload['vendor_id'])) {
                    // Generate vendor_id (doesn't require the item ID)
                    $attributes['vendor_id'] = $this->generateVendorId();
                } else {
                    $attributes['vendor_id'] = $payload['vendor_id'];
                }

                $catalogItem = new CatalogItem($attributes);

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

        // Update with the real internal_id (based on actual ID) if it was temporary
        // Vendor ID is already set correctly (doesn't depend on item ID)
        if (str_starts_with($catalogItem->internal_id, 'SW-TEMP-')) {
            return tap($catalogItem)->update([
                'internal_id' => $this->generateInternalId($catalogItem->id),
            ]);
        }

        return $catalogItem;
    }

    protected function generateVendorId(): string
    {
        $username = (string) ($this->company->username ?? '');
        $initial = strtoupper(substr($username, 0, 2)) ?: 'CO';

        return "{$initial}-" . crc32($this->company->getKey());
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
