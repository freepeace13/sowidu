<?php

namespace App\Services\Order;

use App\Enums\AttachmentType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\Permissions;
use App\Models\Addressbook;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\User;
use App\Services\AddressbookService;
use App\Services\Order\Traits\CanIdentifyOrderParties;
use App\Transformers\Order\OrderTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\WorkLogs\Models\WorkLog;
use Packages\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Models\Activity;

class OrderService
{
    use CanIdentifyOrderParties;

    protected $query;

    public function __construct(protected User $user, protected ?Company $team = null)
    {
        $this->query = $this->newQuery();
    }

    protected function account(): User|Company
    {
        return $this->team ?? $this->user;
    }

    protected function employee(): ?Employee
    {
        return $this->user->teamMembership($this->team);
    }

    public static function make($user, $team = null)
    {
        return new static($user, $team);
    }

    public function setQuery(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function newQuery()
    {
        if (!$this->team) {
            return Order::query()
                ->whereHasMorph(
                    'client',
                    User::class,
                    fn ($q) => $q->whereKey($this->user->getKey()),
                );
        }

        return Order::query()
            ->when($this->team, function (Builder $query) {
                // Login as employee - company is contractor or company is client
                return $query
                    ->whereHasMorph(
                        'client',
                        Company::class,
                        fn ($q) => $q->whereKey($this->team->getKey()),
                    )
                    ->orWhereHasMorph(
                        'contractor',
                        Company::class,
                        fn ($q) => $q->whereKey($this->team->getKey()),
                    );
            }, function (Builder $query) {
                return $query
                    ->whereHasMorph(
                        'client',
                        User::class,
                        fn ($q) => $q->whereKey($this->user->getKey()),
                    );
            });
    }

    /**
     * @return \App\Models\Order|static
     */
    public function __call($method, $parameters)
    {
        $result = $this->query->{$method}(...$parameters);

        if ($result instanceof Builder) {
            return $this;
        }

        $this->setQuery($this->newQuery());

        return $result;
    }

    public function orderByCreatedAt(): self
    {
        $this->query = $this->query->orderBy('created_at', 'DESC');

        return $this;
    }

    public function matchesText($text)
    {
        return $this->when(
            filled($text),
            function ($query) use ($text) {
                return $query->where(
                    fn ($query) => $query
                        ->search($text)
                        ->orWhereHas(
                            'deliveryAddress',
                            function (Builder $query) use ($text) {
                                $query->searchFullAddress($text);
                            },
                        ),
                );
            },
        );
    }

    public function outgoing(): self
    {
        if ($this->team) {
            $this->setQuery(
                Order::query()
                    ->whereHasMorph(
                        'client',
                        Company::class,
                        fn ($q) => $q->whereKey($this->team->getKey()),
                    ),
            );

            return $this;
        }

        $this->query = $this->query
            ->whereHasMorph(
                'client',
                User::class,
                fn ($q) => $q->whereKey($this->user->getKey()),
            );

        return $this;
    }

    public function incoming(): self
    {
        if (!$this->team) {
            $this->setQuery(
                Order::query()
                    ->whereHasMorph(
                        'contractor',
                        User::class,
                        fn ($q) => $q->whereKey($this->user->getKey()),
                    ),
            );

            return $this;
        }

        $this->setQuery(
            Order::query()
                ->whereHasMorph(
                    'contractor',
                    Company::class,
                    fn ($q) => $q->whereKey($this->team->getKey()),
                ),
        );

        return $this;
    }

    public function overview(): self
    {
        if (!$this->team) {
            return $this->outgoing();
        }

        return $this;
    }

    public function orderTypeOnAuth(Order $order): OrderType
    {
        return $this->isIncoming($order) ? OrderType::INCOMING : OrderType::OUTGOING;
    }

    public function isIncoming(Order $order): bool
    {
        if (!$this->team) {
            return false;
        }

        return $order->contractor->is($this->team);
    }

    public function isOutgoing(Order $order)
    {
        return !$this->isIncoming($order);
    }

    public function isInvolvedOnOrder(Order $order): bool
    {
        if ($this->team) {
            return $this->contractorIs($order, $this->team)
                || $this->clientIs($order, $this->team)
                && $this->team->isEmployed($this->user);
        }

        return $this->clientIs($order, $this->user); // Private account
    }

    public function isCurrentlyOwned(Order $order): bool
    {
        if ($this->team) {
            return $this->contractorIs($order, $this->team)
                || $this->clientIs($order, $this->team);
        }

        return $this->authorIs($order, $this->user);
    }

    public function isContractor(Order $order): bool
    {
        return $this->contractorIs($order, $this->team);
    }

    public function authorIs(Order $order, User $user): bool
    {
        return $order->userAuthor->is($user);
    }

    public function isOrderedByCurrentUser(Order $order): bool
    {
        if ($this->team) {
            return $order->client->is($this->team);
        }

        // Check if this order is created by current user
        // OR Order `client` is the current user
        return $this->authorIs($order, $this->user)
            || $order->client()
                ->is($this->user);
    }

    public function getOppositeParty(
        Order $order,
        User $userCauser,
        ?Company $causerRepresenting = null,
    ) {
        $causer = $causerRepresenting ?? $userCauser;

        if (morph_is($causer, User::class)) {
            return $order->contractor;
        }

        if ($this->contractorIs($order, $causer)) {
            // Causer is the contractor - opposite party is the `Client`
            return $order->client;
        }

        // Causer is the client - opposite party is `Contractor`
        return $order->contractor;
    }

    public function getAddressbook(int $addressbookId): Addressbook
    {
        return AddressbookService::make($this->user, $this->team)
            ->where('id', $addressbookId)
            ->firstOrFail();
    }

    public function getClientFromAddressbook(Addressbook $addressbook): Addressbook|User|Company
    {
        throw_validation_unless(
            AddressbookService::make($this->user, $this->team)->isOwned($addressbook),
            'You have no contact with this person, please refresh the page and try again.',
        );

        // Client addressbook has model source OR it IS NOT a foreign
        if (!$addressbook->isForeign()) {
            return $addressbook->source()
                ->first();
        }

        return $addressbook;
    }

    public function getContractorFromAddressbook(Addressbook $addressbook): Addressbook|Company
    {
        throw_validation_unless(
            AddressbookService::make($this->user, $this->team)->isOwned($addressbook),
            'You have no contact with this organization, please refresh the page and try again.',
        );

        // Client addressbook has model source OR it IS NOT a foreign
        if (!$addressbook->isForeign()) {
            return $addressbook->source()
                ->first();
        }

        return $addressbook;
    }

    /**
     * Generate Order Number based on values
     *
     * `first_digit`  = Total # of all orders recorded on DB
     * `second_digit` = Total # of incoming orders on `Contractor` account
     * `third_digit`  = Total # if outgoing orders on `Client` account
     */
    public function generatePermanentOrderNumber(Order $order): string
    {
        $contractor = $order->contractor;
        $client = $order->client;

        $totalOrderCount = Order::query()->confirmed()
            ->count();
        $contractorIncomingOrderCount = $contractor->incomingOrders()
            ->confirmed()
            ->count();
        $clientOutgoingOrderCount = $client->outgoingOrders()
            ->confirmed()
            ->count();

        return collect([
            $totalOrderCount,
            $contractorIncomingOrderCount,
            $clientOutgoingOrderCount,
        ])
            ->transform(fn ($count) => $count += 1)
            ->join('-');
    }

    protected function confirmingOrder(Order $order)
    {
        $order->order_number = $this->generatePermanentOrderNumber($order);
        $order->saveQuietly();
    }

    private function generateTemporaryOrderNumber(): string
    {
        return 'TMP-' . now()->getTimestamp();
    }

    public function newIncomingOrder(
        Addressbook|User|Company $client,
        Addressbook $clientAddressbook,
        array $inputs,
    ): Order {
        $order = $client->orders()
            ->create([
                'order_number' => $this->generateTemporaryOrderNumber(),
                'user_id' => $this->user->id,
                'team_id' => $this->team?->id,
                'type' => Order::INCOMING_TYPE,
                'status' => $inputs['status'] ?? OrderStatus::IN_PREPARATION,
                'description' => $inputs['description'] ?? null,
                'order_date' => $inputs['order_date'],
                'planned_start_date' => $inputs['planned_start_date'] ?? null,
                'planned_finish_date' => $inputs['planned_finish_date'] ?? null,
            ]);

        $order->contractor()
            ->associate($this->account())
            ->save();

        $clientAddressbook->clientOrders()
            ->save($order);

        return $order;
    }

    public function updateClient(Order $order, $clientId): Order
    {
        $clientAddressbook = $this->getAddressbook($clientId);

        $client = $this->getClientFromAddressbook($clientAddressbook);

        $order->client()
            ->associate($client);

        $order->update([
            'client_addressbook_id' => $clientAddressbook->id,
        ]);

        return tap($order)->save();
    }

    public function newOutgoingOrder(
        Company|Addressbook $contractor,
        Addressbook $contractorAddressbook,
        array $inputs,
    ): Order {
        $order = $contractor->deals()
            ->create([
                'order_number' => $this->generateTemporaryOrderNumber(),
                'user_id' => $this->user->id,
                'team_id' => $this->team?->id,
                'type' => Order::OUTGOING_TYPE,
                'status' => $inputs['status'] ?? OrderStatus::IN_PREPARATION,
                'description' => $inputs['description'] ?? null,
                'order_date' => $inputs['order_date'],
                'planned_start_date' => $inputs['planned_start_date'] ?? null,
                'planned_finish_date' => $inputs['planned_finish_date'] ?? null,
            ]);

        $order->client()
            ->associate($this->account())
            ->save();

        $contractorAddressbook->contractorDeals()
            ->save($order);

        return $order;
    }

    public function updateOrderPrimaryDetails(Order $order, array $inputs)
    {
        return $order->update($inputs);
    }

    public function acceptingGetResponseValue(Order $order, int $value)
    {
        $orderResponse = OrderStatus::from($value);

        // Check if accepting order response is `CONTRACTOR` that is `WAITING_FOR_CLIENT_CONFIRMATION`
        if (
            $orderResponse === OrderStatus::WAITING_FOR_CLIENT_CONFIRMATION && $this->authoredByCompany($order)
        ) {
            // True - contractor can force confirm this order
            $orderResponse = OrderStatus::CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION;
        }

        return $orderResponse;
    }

    protected function authoredByCompany(Order $order, ?Company $company = null): bool
    {
        $company = $company ?? $this->team;

        return filled($order->team_id) && $order->team_id === $company->getKey();
    }

    public function canResponseToOrder(Order $order): bool
    {
        return $this->isRequiresResponse($order);
    }

    public function canForceConfirm(Order $order, OrderStatus $responseStatus)
    {
        return $this->team
            && Arr::has(
                $this->getResponseDialogData(
                    $this->getNeededResponseValue($order),
                ),
                'force_confirm',
            );
    }

    public function isRequiresResponse(Order $order): bool
    {
        if (blank($toOrderStatus = $this->getNeededResponseValue($order))) {
            return false;
        }

        return filled(
            Arr::get($this->getResponseDialogData($toOrderStatus), 'accept_button_label'),
        );
    }

    public function getNeededResponseValue(Order $order): ?OrderStatus
    {
        return ResponseToOrder::make($order, $this->user, $this->team)->orderStatus();
    }

    public function getResponseDialogData(OrderStatus $toOrderStatus): array
    {
        return DialogForOrderStatus::make($toOrderStatus)->build();
    }

    public function rejectOrder(Order $order): Order
    {
        return tap($order)->update([
            'status' => OrderStatus::CANCELLED,
        ]);
    }

    public function disapproveReview(Order $order): Order
    {
        return tap($order)->update(['status' => OrderStatus::REJECT]);
    }

    public function acceptOrder(Order $order, OrderStatus $toUpdatedStatus): Order
    {
        try {
            if ($toUpdatedStatus === OrderStatus::CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION) {
                // Force confirm!
                $toUpdatedStatus = OrderStatus::COMMISSIONED;
            }

            if ($toUpdatedStatus === OrderStatus::WAITING_FOR_CLIENT_REVIEW) {
                // Force fulfill order!
                $toUpdatedStatus = OrderStatus::FINISHED;
            }

            // Check if to updated status is `COMMISSIONED`
            if ($toUpdatedStatus === OrderStatus::COMMISSIONED) {
                $this->confirmingOrder($order);
            }

            return tap($order)->update(['status' => $toUpdatedStatus]);
        } catch (\Throwable $th) {
            if (
                app()->environment('local')
            ) {
                flash_error($th->getMessage());

                return $order;
            }

            flash_error(trans('alerts.error.500'));

            return $order;
        }
    }

    public function attachMedia(Order $order, Media $mediaFile): Order
    {
        return $this->attach($order, $mediaFile, AttachmentType::MEDIA);
    }

    public function attachDocument(Order $order, Media $mediaFile): Order
    {
        return $this->attach($order, $mediaFile, AttachmentType::DOCUMENT);
    }

    public function attachIncomingInvoice(Order $order, Media $mediaFile): Order
    {
        return $this->attach($order, $mediaFile, AttachmentType::INCOMING_INVOICE);
    }

    public function attachOutgoingInvoice(Order $order, Media $mediaFile): Order
    {
        return $this->attach($order, $mediaFile, AttachmentType::OUTGOING_INVOICE);
    }

    public function attach(Order $order, Media $mediaFile, AttachmentType $attachmentType): Order
    {
        $order->attachments()
            ->attach($mediaFile, [
                'type' => $attachmentType,
                'user_id' => $this->user->getKey(),
                'team_id' => $this->team?->getKey(),
            ]);

        return $order;
    }

    public function mediaIsAttached(Order $order, Media $media): bool
    {
        return OrderAttachment::query()->where([
            'order_id' => $order->getKey(),
            'media_file_id' => $media->getKey(),
        ])
            ->exists();
    }

    public function mediaIsNotAttached(Order $order, Media $media): bool
    {
        return !$this->mediaIsAttached($order, $media);
    }

    public function detachAttachment(Order $order, Media $mediaFile): Order
    {
        $order->attachments()
            ->detach($mediaFile);

        return $order;
    }

    public function attachmentReadableFor($query, $account)
    {
        return $query->where(function (Builder $query) use ($account) {
            return $query->whereHas(
                'shares',
                fn ($query) => $query->whereReadableFor($account),
            )
                ->orWhere(
                    fn ($query) => $query
                        ->where('model_id', $account->getKey())
                        ->where('model_type', $account->getMorphClass()),
                );
        });
    }

    /**
     * @return Builder
     */
    public function attachmentMedias(User|Employee $account, Order $order, array $filters = [])
    {
        return $this->attachmentReadableFor(
            $order->medias()
                ->when(
                    filled($filters),
                    fn (Builder $query) => $query->filter($filters),
                ),
            $account,
        );
    }

    /**
     * @return Builder
     */
    public function attachmentDocuments(User|Employee $account, Order $order)
    {
        return $this->attachmentReadableFor($order->documents(), $account);
    }

    /**
     * @return Builder
     */
    public function attachmentIncomingInvoices(User|Employee $account, Order $order)
    {
        return $this->attachmentReadableFor($order->incomingInvoices(), $account);
    }

    /**
     * @return Builder
     */
    public function attachmentOutgoingInvoices(User|Employee $account, Order $order)
    {
        return $this->attachmentReadableFor($order->outgoingInvoices(), $account);
    }

    public function canUpdateOrder(Order $order): bool
    {
        return $this->contractorIs($order, $this->team) && $this->employee()
            ->hasPermissionTo(Permissions::CAN_UPDATE_ORDER);
    }

    public function canStopTimeLog(Order $order, WorkLog $workLog): bool
    {
        // Check if last time log is created by current user or the current user is the `Founder` of the company
        return $workLog->causer->is($this->user) || $this->team->isFounder($this->user);
    }

    public function canStartTimeLog(Order $order): bool
    {
        return $this->team->isFounder($this->user) || $this->team->isEmployed($this->user);
    }

    public function orderTimeLogs(Order $order): Builder
    {
        return (new OrderTimeLogService($order))->timeLogs();
    }

    public function orderTimelines(Order $order)
    {
        return Activity::forSubject($order);
    }

    public function addProducts(Order $order, array $products)
    {
        return OrderItemService::make(
            $order,
            $this->user,
            $this->team,
        )->addProducts($products);
    }

    public function addDeliveryTicketMaterial(Order $order, array $materials)
    {
        return OrderItemService::make(
            $order,
            $this->user,
            $this->team,
        )->addDeliveryTicketMaterials($materials);
    }

    public function countOfIncoming(array $filters = []): int
    {
        return $this
            ->incoming()
            ->filter($filters)
            ->count();
    }

    public function countOfOutgoing(array $filters = []): int
    {
        return $this
            ->outgoing()
            ->filter($filters)
            ->count();
    }

    public function countOfIncomingRequiresResponse(array $filters = []): int
    {
        return $this
            ->incoming()
            ->with(['client'])
            ->filter($filters)
            ->get()
            ->filter(fn ($order) => $this->isRequiresResponse($order))
            ->count();
    }

    public function countOfOutgoingRequiresResponse(array $filters = []): int
    {
        return $this
            ->outgoing()
            ->with(['client'])
            ->filter($filters)
            ->get()
            ->filter(fn ($order) => $this->isRequiresResponse($order))
            ->count();
    }

    public function filters(array $filters = []): self
    {
        $this->query
            ->when(
                $q = $filters['q'] ?? null,
                fn ($query) => $this->matchesText($q),
                fn ($query) => $query->orderBy('created_at', 'desc'),
            );

        return $this;
    }

    public function transform(Order $order)
    {
        $order->loadMissing(['contractor']);

        $transformer = (new OrderTransformer($order));
        $timeLogService = OrderTimeLogService::make($order);

        $orderType = $this->orderTypeOnAuth($order);
        $oppositeParty = $order->getOppositeParty($this->user, $this->team);

        if ($orderType === OrderType::INCOMING) {
            $transformer = $transformer->withClientPrimaryDetails($oppositeParty);
        } else {
            $transformer = $transformer->withContractorDetails($oppositeParty);
        }

        return $transformer
            ->withOrderType($orderType)
            ->withIsRequireResponse($this->user, $this->team)
            ->withDeliveryAddress()
            ->withStatus($this->user, $this->team)
            ->withTotalTimeRendered($timeLogService->getOrderTotalTimeRendered())
            ->withInvoiceCount()
            ->resolve();
    }

    public function deliveryTicketMaterials(Order $order)
    {
        return $order->loadMissing(['deliveryTicketsMaterials'])
            ->deliveryTicketsMaterials()
            ->get();
    }
}
