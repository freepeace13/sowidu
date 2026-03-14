<?php

namespace Modules\Invoicify\Services;

use App\Enums\InvoiceKind;
use App\Enums\InvoiceStatus;
use App\Events\Invoice\InvoiceWasFullyPaid;
use App\Models\Company;
use App\Models\DeductionManual;
use App\Models\DeliveryTicket;
use App\Models\DeliveryTicketMaterial;
use App\Models\Invoice;
use App\Models\InvoiceDeduction;
use App\Models\InvoiceItem;
use App\Models\InvoiceManualItem;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use App\Models\WorkLog;
use App\Services\CacheService;
use App\Services\CompanyService;
use App\Services\Order\OrderItemService;
use App\Transformers\EmployeeRateTransformer;
use App\Transformers\UserTransformer;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Invoicify\Traits\WithInvoiceSettings;

class InvoiceService
{
    use WithInvoiceSettings;

    public function __construct(protected Invoice $invoice) {}

    public static function run(Invoice $invoice)
    {
        return new self($invoice);
    }

    public function getPayments(): Collection
    {
        return $this->invoice->payments()
            ->get();
    }

    public function resetPreviewLayout()
    {
        return $this->invoice->update([
            'preview_layout' => null,
        ]);
    }

    public function getPaymentTerms(): int
    {
        return (int) $this->invoice->company
            ->settings()
            ->invoiceDefaults()
            ->get('payment_terms');
    }

    public function getManagingDirector(): array
    {
        return $this->invoice->company
            ->settings()
            ->invoiceDefaults()
            ->get('managing_director', []);
    }

    public function getPaymentTermsInstructions(): ?string
    {
        return (int) $this->invoice->company
            ->settings()
            ->invoiceDefaults()
            ->get('payment_terms_instructions');
    }

    public function getOrder(): Order
    {
        return $this->invoice->order()
            ->first();
    }

    public function attachTaxes()
    {
        // Get company `default` taxes
        $taxes = $this->invoice->company()
            ->first()
            ->taxes()
            ->where('is_default', true)
            ->pluck('id')
            ->toArray();

        $this->invoice->taxes()
            ->attach($taxes);
    }

    public function saveInvoiceDefaults(Company $company): void
    {
        $this->invoice->update([
            'notes' => $company->settings()
                ->invoiceDefaults()
                ->get('payment_terms_instructions'),
        ]);
    }

    public function removeDeduction() {}

    public function attachDeduction(Invoice|DeductionManual $deduction)
    {
        if (
            $this->invoice->deductions()
                ->whereMorphedTo('deductible', $deduction)
                ->exists()
        ) {
            return;
        }

        InvoiceDeduction::make()
            ->invoice()
            ->associate($this->invoice)
            ->deductible()
            ->associate($deduction)
            ->save();
    }

    public function attachInvoiceDeductions(array $invoiceDeductionIds)
    {
        Invoice::query()
            ->whereIn('id', $invoiceDeductionIds)
            ->get()
            ->each(fn (Invoice $invoiceDeduction) => $this->attachDeduction($invoiceDeduction));
    }

    protected function saveLineItems($lineItems)
    {
        return $this->invoice->items()
            ->saveMany($lineItems);
    }

    public function generateItemsForFinalInvoice(User $user)
    {
        $order = $this->invoice->order;

        // Get order `used_products`
        $this->saveLineItems(
            $order
                ->usedProducts()
                ->get()
                ->map(
                    // Generate line items from used_products
                    fn (OrderProduct $orderProduct) => $this->productToLineItem($orderProduct, $user),
                ),
        );

        // Get order delivery_ticket_materials
        $this->saveLineItems(
            $order
                ->deliveryTickets()
                ->with([
                    'materials',
                ])
                ->get()
                ->map(
                    fn ($deliveryTicket) => $deliveryTicket
                        ->materials
                        ->map(
                            fn ($material) => $this->materialToLineItem(
                                $deliveryTicket,
                                $material,
                                $user,
                            ),
                        ),
                )
                ->flatten(1),
        );

        // Generate line items from `work_logs`
        $company = $this->invoice->company()
            ->first();
        $this->saveLineItems(
            $order
                ->workLogs()
                ->closed()
                ->get()
                ->map(
                    fn ($workLog) => $this->workLogToLineItem($workLog, $user, $company),
                ),
        );
    }

    public function autoGenerateItems(User $user)
    {
        $invoice = $this->invoice;
        $order = $invoice->order;

        if ($this->invoice->kind === InvoiceKind::FINAL) {
            // Get all items from order whether billed or not
            $this->generateItemsForFinalInvoice($user);

            return;
        }

        // Get order `used_products`
        $this->saveLineItems(
            $order
                ->unPaidAndUnInvoicedUsedProducts()
                ->get()
                ->map(
                    // Generate line items from used_products
                    fn (OrderProduct $orderProduct) => $this->productToLineItem($orderProduct, $user),
                ),
        );

        // Get order delivery_ticket_materials
        $this->saveLineItems(
            $order
                ->deliveryTickets()
                ->doesntHave('invoices')
                ->with([
                    'materials' => fn ($q) => $q->unInvoiced(),
                ])
                ->get()
                ->map(
                    fn ($deliveryTicket) => $deliveryTicket
                        ->materials
                        ->map(
                            fn ($material) => $this->materialToLineItem(
                                $deliveryTicket,
                                $material,
                                $user,
                            ),
                        ),
                )
                ->flatten(1),
        );

        // Generate line items from `work_logs`
        $company = $this->invoice->company()
            ->first();
        $this->saveLineItems(
            $order
                ->workLogs()
                ->unInvoiced()
                ->closed()
                ->get()
                ->map(
                    fn ($workLog) => $this->workLogToLineItem($workLog, $user, $company),
                ),
        );
    }

    protected function workLogToLineItem(
        WorkLog $workLog,
        User $invoiceAuthor,
        Company $company,
    ) {
        $workLogOwner = $workLog->user;

        $name = $workLogOwner->full_name;

        // Get price from `Employee`
        $companyService = CompanyService::make($company);
        $employee = $companyService->getEmployeeFromUser($workLogOwner);
        $rate = $companyService->getEmployeeRateFromUser($workLogOwner);

        $price = $rate->rate;

        $durationInMinutes = ceil($workLog->duration_in_seconds / 60);
        $quantity = number_format($durationInMinutes / 60, 2);

        $startedAt = $workLog->started_at->format('d.m.Y H:i:s');
        $endedAt = $workLog->ended_at->format('d.m.Y H:i:s');
        $description = "{$workLog->duration_for_human}";

        $invoiceItem = $this->createInvoiceItem(
            $invoiceAuthor,
            $name,
            $price,
            $quantity,
            $description,
            [
                'user' => UserTransformer::make($workLogOwner)->resolve(),
                'work_log' => array_merge($workLog->toArray(), [
                    'started_at' => $startedAt,
                    'ended_at' => $endedAt,
                ]),
                'employee_rate' => EmployeeRateTransformer::make($rate)->resolve(),
                'employee' => [
                    'uuid' => $employee->uuid,
                ],
            ],
        );

        $invoiceItem->item()
            ->associate($workLog);

        return $invoiceItem;
    }

    public function materialToLineItem(
        DeliveryTicket $deliveryTicket,
        DeliveryTicketMaterial $material,
        User $user,
    ): InvoiceItem {
        $details = $material->details;
        $details->put(
            'delivery_ticket',
            $deliveryTicket->only([
                'internal_id',
                'external_id',
                'id',
            ]),
        );

        $name = $details->get('external_id') ?? $details->get('internal_id') . ' ' . $details->get('name');

        $invoiceItem = $this->createInvoiceItem(
            $user,
            $name,
            $details->get('selling_price', 0),
            $material->quantity,
            $details->get('description') ?? $details->get('short_description') ?? '',
            $details->toArray(),
        );

        $invoiceItem->item()
            ->associate($material);

        return $invoiceItem;
    }

    public function addTickets(User $user, Company $company, array $tickets)
    {
        $lineItems = collect($tickets)
            ->map(function ($ticketId) use ($user) {
                return $this->ticketToLineItem(
                    DeliveryTicket::findOrFail($ticketId),
                    $user,
                );
            });

        $this->invoice->items()
            ->saveMany($lineItems);
    }

    protected function createInvoiceItem(
        User $user,
        string $name,
        int|float $price,
        int|float $quantity = 1,
        ?string $description = null,
        array $details = [],
    ): InvoiceItem {
        $invoiceItem = InvoiceItem::make([
            'name' => trim($name),
            'quantity' => $quantity,
            'price' => $price,
            'description' => $description,
            'details' => $details,
        ]);

        $invoiceItem->user()
            ->associate($user);

        return $invoiceItem;
    }

    protected function ticketToLineItem(DeliveryTicket $deliveryTicket, User $user): InvoiceItem
    {
        $name = trans('invoices.labels.delivery-ticket-number') . $deliveryTicket->external_id ?? $deliveryTicket->internal_id;
        $price = 0;
        $quantity = 1;
        $description = trans('delivery_tickets.form.deliverer') . ': ' . $deliveryTicket->deliverer->name;

        $invoiceItem = $this->createInvoiceItem(
            $user,
            $name,
            $price,
            $quantity,
            $description,
            $deliveryTicket->toArray(),
        );

        $invoiceItem->item()
            ->associate($deliveryTicket);

        return $invoiceItem;
    }

    protected function productToLineItem(OrderProduct $orderProduct, User $user): InvoiceItem
    {
        $details = $orderProduct->details;
        $name = $details?->external_id ?? $details?->internal_id . ' ' . $details->name;

        $invoiceItem = $this->createInvoiceItem(
            $user,
            $name,
            $details->selling_price,
            $orderProduct?->quantity ?? 1,
            $details?->description ?? $details?->short_description ?? '',
            $orderProduct->toArray(),
        );

        $invoiceItem->item()
            ->associate($orderProduct);

        return $invoiceItem;
    }

    public function addManualItem(
        User $user,
        Company $company,
        InvoiceManualItem $invoiceManualItem,
    ) {
        $invoice = $this->invoice;

        // // Save to OrderProducts first
        // $orderProducts = OrderItemService::make(
        //     $invoice->order,
        //     $user,
        //     $company,
        // )->addProducts($products);

        $invoiceItem = $this->createInvoiceItem(
            $user,
            $invoiceManualItem->name,
            $invoiceManualItem->selling_price,
            $invoiceManualItem->quantity,
            $invoiceManualItem->description ?? '',
            $invoiceManualItem->toArray(),
        );

        $invoiceItem->item()
            ->associate($invoiceManualItem);

        $invoice->items()
            ->save($invoiceItem);
    }

    public function addProducts(User $user, Company $company, array $products)
    {
        $invoice = $this->invoice;

        // Save to OrderProducts first
        $orderProducts = OrderItemService::make(
            $invoice->order,
            $user,
            $company,
        )->addProducts($products);

        $lineItems = collect($orderProducts)
            ->map(fn ($orderProduct) => $this->productToLineItem($orderProduct, $user));
        $invoice->items()
            ->saveMany($lineItems);
    }

    public function addUsedProduct(User $user, OrderProduct $orderProduct): InvoiceItem
    {
        $invoiceItem = $this->productToLineItem($orderProduct, $user);

        $this->invoice->items()
            ->save($invoiceItem);

        return $invoiceItem;
    }

    public function currency(): string
    {
        return CacheService::getCompanyCurrency($this->invoice->company);
    }

    public function markAsPaid()
    {
        $invoice = $this->invoice;

        $invoice->update(['status' => InvoiceStatus::PAID()]);

        // Mark items (products/materials) as paid too
        $invoice->items()
            ->with(['item'])
            ->get()
            ->each(function (InvoiceItem $invoiceItem) {
                $item = $invoiceItem->item;

                if (morph_is($item, DeliveryTicket::class)) {
                    return;
                }

                // Mark this invoice-item as paid
                $item->update(['is_paid' => true]);
            });

        event(new InvoiceWasFullyPaid($invoice));
    }

    public function sendToClient(Invoice $invoice)
    {
        $paymentTerms = $this->getPaymentTerms();

        $invoice->update([
            'status' => InvoiceStatus::SENT,
            'send_date' => today(),
            'payment_date' => now()->addDays($paymentTerms),
        ]);
    }

    /** @return User|Company|\App\Models\Addressbook */
    public function getPayee()
    {
        return $this->invoice->order->client;
    }

    /** @return User|Company|\App\Models\Addressbook */
    public function getCustomer()
    {
        return $this->getPayee();
    }

    protected function countCustomerInvoices(): int
    {
        $customer = $this->getCustomer();

        $customerOrders = Order::query()
            ->select(['id', 'clientable_id', 'clientable_type'])
            ->where([
                'clientable_id' => $customer->getKey(),
                'clientable_type' => $customer->getMorphClass(),
            ])
            ->withCount([
                'invoices' => fn ($query) => $query->exceptDraft(),
            ])
            ->without(['client'])
            ->get();

        return $customerOrders->sum('invoices_count');
    }

    /**
     * Generate Invoice Number based on values (100-22-02)
     *
     * `first_digit`  = Total # of all `invoices` recorded on DB
     * `second_digit` = Total # of incoming `invoices` on `Contractor` account
     * `third_digit`  = Total # if outgoing `invoices` on `Client` account
     */
    public function savePermanentInternalId()
    {
        $this->invoice = $this->invoice->fresh();

        if ($this->invoice->status->value == InvoiceStatus::DRAFT->value) {
            return;
        }

        $contractor = $this->invoice->contractor;

        $totalInvoiceCount = Invoice::query()->exceptDraft()
            ->count();

        $contractorInvoicesCount = $contractor
            ->invoices()
            ->exceptDraft()
            ->count();

        $customerInvoicesCount = $this->countCustomerInvoices();

        $invoiceNumber = $this->getPermanentInternalId(
            $totalInvoiceCount,
            $contractorInvoicesCount,
            $customerInvoicesCount,
        );

        $this->invoice->update([
            'internal_id' => $invoiceNumber,
        ]);

        return $invoiceNumber;
    }

    protected function getPermanentInternalId(int $totalInvoiceCount, int $contractorInvoicesCount, int $customerInvoicesCount): string
    {
        $increment = 1;

        do {
            $invoiceNumber = 'INV ' . collect([
                $totalInvoiceCount,
                $contractorInvoicesCount,
                $customerInvoicesCount,
            ])
                ->transform(fn ($count) => $count += 1)
                ->map(fn ($count) => Str::padLeft($count, 3, '0'))
                ->join('-');

            $totalInvoiceCount += $increment;
        } while (
            Invoice::query()->where('internal_id', $invoiceNumber)
                ->exists()
        );

        return $invoiceNumber;
    }

    public function saveTemporaryInternalId()
    {
        return $this->invoice->update([
            'internal_id' => 'TMP-' . now()->getTimestamp(),
        ]);
    }

    public function itemsCanBeUpdated(): bool
    {
        return $this->invoice->isDraft();
    }

    public function itemsCannotBeUpdated(): bool
    {
        return !$this->itemsCanBeUpdated();
    }

    /** @param  \Illuminate\Support\Collection  $items */
    public function checkEmployeeRateNotSet($items = []): bool
    {
        if (blank($items)) {
            $items = $this->invoice->items()
                ->get();
        }

        // Check all invoice Items if employee has rate
        return $items->contains(function (InvoiceItem $item) {
            if ($item->isWorkLog()) {
                $workLog = $item->item;
                $company = $workLog->loadMissing(['company', 'user'])
                    ->company;
                $employee = CompanyService::make($company)->getEmployeeFromUser($workLog->user);

                return blank($employee->rate);
            }
        });
    }

    public function getTaxes($columns = ['*'])
    {
        return $this->invoice->taxes()
            ->get($columns);
    }

    public function paymentService(): InvoicePaymentService
    {
        return InvoicePaymentService::run($this->invoice);
    }

    public function summaryService(): InvoiceSummaryService
    {
        return InvoiceSummaryService::run($this->invoice);
    }

    public function totalWage(): float|int
    {
        return $this->summaryService()
            ->totalWage();
    }

    public function updateInvoiceStatus(): void
    {
        $payment = $this->paymentService();

        $status = $this->invoice->status;

        if ($payment->isOverPaid()) {
            $status = InvoiceStatus::OVERPAID;
        } elseif ($payment->isFullyPaid()) {
            $status = InvoiceStatus::PAID;
        } elseif ($payment->hasPayments()) {
            $status = InvoiceStatus::PARTIALLY_PAID;
        } else {
            $status = InvoiceStatus::SENT;
        }

        $this->invoice->update(['status' => $status]);
    }

    public function totalAmount(): string
    {
        return number_format($this->paymentService()
            ->grandTotal(), 2);
    }

    public function getAddableTaxes(): Collection
    {
        $addedTaxesIds = $this->getTaxes()
            ->pluck('id')
            ->toArray();

        return $this->invoice->company->taxes()
            ->whereNotIn('id', $addedTaxesIds)
            ->get();
    }

    public function getWorkLogs(): Collection
    {
        return $this->invoice->workLogs()
            ->get();
    }

    public function getConstructionSite(): \App\Models\Place
    {
        if (filled($this->invoice->construction_site_id)) {
            return $this->invoice->constructionSite()->first();
        }

        return $this->invoice->order->deliveryAddress;
    }

    public function getExecutionPeriod()
    {
        if (
            $this->invoice->execution_period_start || $this->invoice->execution_period_end
        ) {

            return [
                'started_at' => $this->invoice->execution_period_start,
                'ended_at' => $this->invoice->execution_period_end,
            ];
        }

        return $this->estimateExecutionPeriod();
    }

    protected function estimateExecutionPeriod(): array
    {
        $workLogs = $this->getWorkLogs()
            ->map(fn ($workLog) => $workLog->item);

        return [
            'started_at' => $this->invoice->execution_period_start ?? $workLogs->min('started_at'),
            'ended_at' => $this->invoice->execution_period_end ?? $workLogs->max('ended_at'),
        ];
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

        // Group work_logs based on employee and merge time and price
        $workLogs = $items
            ->filter(fn ($item) => $item['is_work_log'] ?? false)
            ->groupBy('details.employee_id')
            ->map(function ($groupItem) {
                $itemDetails = $groupItem->first();
                $quantity = $groupItem->sum('quantity');
                $subtotal = $groupItem->sum('subtotal');

                $durationInMinutes = ceil($quantity * 60);
                $timeDurationForHuman = $this->getTimeDurationForHuman($durationInMinutes);

                return array_merge(
                    $itemDetails,
                    [
                        'quantity' => number_format($quantity, 2),
                        'quantity_formatted' => format_number($quantity, 2),
                        'subtotal' => $subtotal,
                        'subtotal_formatted' => number_to_money(
                            $subtotal,
                            $this->currency(),
                        ),
                        'description' => $timeDurationForHuman,

                    ],
                );
            });

        return $items
            ->reject(fn ($item) => $toRemoved->contains($item['id']) || $item['is_work_log'] ?? false)
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
            ->merge($workLogs)
            ->sortByDesc('is_work_log')
            ->values()
            ->toArray();
    }
}
