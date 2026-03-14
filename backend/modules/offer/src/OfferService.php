<?php

namespace Modules\Offer;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Offer\Contracts\External\AddressbookServiceContract;
use Modules\Offer\Contracts\External\CacheServiceContract;
use Modules\Offer\Enums\OfferActionType;
use Modules\Offer\Enums\OfferStatus;
use Modules\Offer\Models\Offer;
use Modules\Offer\Models\OfferItem;
use Modules\Offer\Support\OfferCalculationService;

class OfferService
{
    protected ?AddressbookServiceContract $addressbookService = null;

    protected ?CacheServiceContract $cacheService = null;

    public function __construct(protected Offer $offer) {}

    public static function make(Offer $offer): static
    {
        return new static($offer);
    }

    public function withAddressbookService(AddressbookServiceContract $service): static
    {
        $this->addressbookService = $service;

        return $this;
    }

    public function withCacheService(CacheServiceContract $service): static
    {
        $this->cacheService = $service;

        return $this;
    }

    protected function getAddressbookService(): AddressbookServiceContract
    {
        return $this->addressbookService ??= app(AddressbookServiceContract::class);
    }

    protected function getCacheService(): CacheServiceContract
    {
        return $this->cacheService ??= app(CacheServiceContract::class);
    }

    public function addItem(
        string $name,
        float $price,
        string $description,
        array $details,
        float $quantity = 1,
    ) {
        $this->offer->items()
            ->save(
                new OfferItem([
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'details' => collect($details),
                    'quantity' => $quantity,
                ]),
            );
    }

    public function calculation(): OfferCalculationService
    {
        return new OfferCalculationService($this->offer);
    }

    public function saveOfferTotals(): Offer
    {
        $this->offer->subtotal = $this->calculation()
            ->subtotal();
        $this->offer->net_amount = $this->calculation()
            ->subtotal();
        $this->offer->total_vat = $this->calculation()
            ->totalVats();
        $this->offer->grand_total = $this->calculation()
            ->grandTotal();

        return tap($this->offer)->save();
    }

    public function logAction(OfferActionType $actionType, Model $author): void
    {
        $this->offer->history()
            ->create([
                'author_id' => $author->id,
                'action_type' => $actionType,
            ]);
    }

    public function accept()
    {
        $this->offer->update([
            'status' => OfferStatus::ACCEPTED,
        ]);
    }

    public function send()
    {
        if ($this->offer->isDraft() === false) {
            return;
        }

        $this->offer->update([
            'status' => OfferStatus::PENDING,
            'internal_id' => $this->generateInternalId(),
        ]);
    }

    public function reject()
    {
        $this->offer->update([
            'status' => OfferStatus::REJECTED,
        ]);
    }

    public function cancel()
    {
        $this->offer->update([
            'status' => OfferStatus::CANCELLED,
        ]);
    }

    public function generateInternalId()
    {
        $offer = $this->offer->fresh();

        $issuer = $offer->company;

        $allSentOffers = Offer::query()->exceptDraft()
            ->count();

        $countIssuerSentOffers = $issuer
            ->offers()
            ->exceptDraft()
            ->count();

        return $this->validateInternalId(
            $allSentOffers,
            $countIssuerSentOffers,
        );
    }

    protected function validateInternalId(
        int $total,
        int $issuerOffersCount,
    ): string {
        $increment = 1;
        $prefix = config('offer.number_prefix');
        $year = today()->year;

        do {
            $internalId = collect([
                $total,
                $issuerOffersCount,
            ])
                ->transform(fn ($count) => $count += 1)
                ->map(fn ($count) => Str::padLeft($count, 3, '0'))
                ->join('-');

            $total += $increment;
        } while (
            Offer::query()->where('internal_id', $internalId)
                ->exists()
        );

        return collect([
            $prefix,
            $year,
            $internalId,
        ])->join('-');
    }

    public function toOrder(): array
    {
        // Only generate orders from accepted offers
        if ($this->offer->status !== OfferStatus::ACCEPTED) {
            return [];
        }

        $constructionSite = null;

        if (filled($this->offer->construction_site_id)) {
            $constructionSite = $this->offer->constructionSite()
                ->first();
        } else {
            $recipient = $this->offer->recipient()
                ->first();
        }

        $description = $this->offer->description;
        if (blank($description)) {
            $description = 'Offer #' . $this->offer->internal_id;
        }

        return [
            'description' => $description,
            'order_date' => today()->toDateString(),
            'planned_start_date' => null,
            'planned_finish_date' => null,
            'contractor_id' => $this->offer->company_id,
            'client_id' => $this->offer->recipientable_id,
            'delivery_address' => [
                'id' => $constructionSite ? $constructionSite->id : $recipient->current_place_id,
            ],
        ];
    }

    public function groupItems(Collection $items): array
    {
        $toRemoved = collect([]); // Item Id's
        $toMerged = collect([]);

        // Group same items and merge quantity and price
        $items
            ->filter(fn ($item) => $item['is_order_product'] ?? false)
            ->map(fn ($item) => [
                'id' => $item['id'],
                'item_id' => data_get($item, 'details.id'),
                'quantity' => number_to_raw($item['quantity']),
                'price' => $item['price'],
                'parent_item_id' => data_get($item, 'parent_item_id'),
            ])
            // ->groupBy('item_id')
            ->groupBy(['item_id', 'price'], true)
            ->flatten(1)
            ->each(function ($groupItem) use ($toRemoved, $toMerged) {
                $quantity = $groupItem->sum('quantity');

                // Map $groupItem except first item
                $groupItem->skip(1)
                    ->each(fn ($item) => $toRemoved->push($item['id']));

                $firstItem = $groupItem->first();
                if ($firstItem) {
                    $toMerged->push([
                        'quantity' => $quantity,
                        'id' => $firstItem['id'],
                    ]);
                }
            });

        return $items
            ->values()
            ->map(function (array $invoiceItem) use ($toMerged) {
                $itemId = $invoiceItem['id'];
                $mergedItem = $toMerged->firstWhere('id', $itemId);

                if ($mergedItem) {
                    $mergeQuantity = $mergedItem['quantity'];
                    $subtotal = $mergeQuantity * $invoiceItem['price'];

                    $invoiceItem['quantity'] = $mergeQuantity;
                    $invoiceItem['quantity_formatted'] = format_number($mergeQuantity, 2);
                    $invoiceItem['subtotal'] = $subtotal;
                    $invoiceItem['subtotal_formatted'] = number_to_money(
                        $subtotal,
                        $this->currency(),
                    );
                }

                return $invoiceItem;
            })
            ->values()
            ->toArray();
    }

    public function getTimeDurationForHuman(float $minutes)
    {
        $duration = $minutes * 60;
        // Convert seconds to CarbonInterval
        $interval = CarbonInterval::minutes($duration)->cascade();

        // Calculate total hours to prevent day conversion
        $totalHours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);

        // // Create a new interval with total hours and minutes
        $interval = CarbonInterval::hours($totalHours)->minutes($minutes);

        return $interval->forHumans([
            'join' => ' and ',       // Join hours and minutes with "and"
            'parts' => 2,            // Limit to 2 parts: hours and minutes
            'minimumUnit' => 'minute', // Only show up to minutes
        ]);
    }

    public function currency(): string
    {
        return $this->getCacheService()->getCompanyCurrency($this->offer->company);
    }

    public function storagePath(): string
    {
        return storage_path('app/offers/' . $this->offer->internal_id . '.pdf');
    }

    public function attachDefaultTaxes(): void
    {
        $company = $this->offer->loadMissing('company')
            ->company;

        $company->taxes()
            ->default()
            ->get(['name', 'rate', 'id'])
            ->each(function (Model $tax) {
                $this->offer->properties()
                    ->taxes()
                    ->attach($tax->only('name', 'rate', 'id'));
            });
    }

    public function taxesAmounts(): array
    {
        $subtotal = $this->calculation()
            ->subtotal();

        return $this->offer->properties()
            ->taxes()
            ->toCollection()
            ->all()
            ->map(function ($tax) use ($subtotal) {
                return [
                    'name' => $tax['name'],
                    'rate' => $tax['rate'],
                    'amount' => $this->calculation()
                        ->applyTaxRate(
                            $subtotal,
                            $tax['rate'],
                        ),
                ];
            })
            ->toArray();
    }

    public function isParticipant(Model $user, ?Model $company = null): bool
    {
        if ($company) {
            return $this->offer->isOwnedByCompany($company);
        }

        return $this->isRecipient($user);
    }

    public function isRecipient(Model $user): bool
    {
        // Check if the user is the recipient of the offer
        $addressbookIds = $this->getAddressbookService()->getAddressbookIdsFromUser($user);

        return $addressbookIds->contains($this->offer->recipientable_id);
    }

    public function saveOfferConfigurationDefaults()
    {
        $notes = $this->offer->company->offerConfiguration->terms_and_conditions;

        $this->offer->update([
            'notes' => $notes,
        ]);
    }
}
