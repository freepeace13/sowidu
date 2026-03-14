<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\Traits\CanIdentifyOrderParties;

class ResponseToOrder
{
    use CanIdentifyOrderParties;

    public function __construct(
        protected Order $order,
        protected User $user,
        protected ?Company $team = null,
    ) {}

    protected function account(): User|Company
    {
        return $this->team ?? $this->user;
    }

    public static function make($order, $user, $team)
    {
        return new static($order, $user, $team);
    }

    public function orderStatus(): ?OrderStatus
    {
        $status = $this->order->status;
        $response = null;

        if ($status === OrderStatus::IN_PREPARATION) {
            if ($this->team) {
                $currentCompany = $this->team;

                // Check if the current company is the author
                if ($this->authoredByCompany($currentCompany)) {
                    // Check if client is not registered - then contractor can confirm the order
                    if ($this->clientIsForeign($this->order)) {
                        return OrderStatus::CONTRACTOR_WAITING_FOR_CLIENT_CONFIRMATION;
                    }

                    if ($this->clientIs($this->order, $currentCompany)) {
                        return OrderStatus::WAITING_FOR_CONTRACTOR_CONFIRMATION;
                    }

                    if ($this->contractorIs($this->order, $currentCompany)) {
                        return OrderStatus::WAITING_FOR_CLIENT_CONFIRMATION;
                    }
                }

                if (
                    $this->contractorIs($this->order, $currentCompany)
                    || $this->clientIs($this->order, $currentCompany)
                ) {
                    return OrderStatus::COMMISSIONED;
                }
            }

            if (!$this->authoredByUser($this->user)) {
                return OrderStatus::COMMISSIONED;
            }

            return OrderStatus::WAITING_FOR_CONTRACTOR_CONFIRMATION;
        }

        $order = $this->order;

        if ($status === OrderStatus::COMMISSIONED) {
            return $this->contractorIs($order, $this->team)
                ? OrderStatus::STARTED
                : OrderStatus::WAITING_FOR_CONTRACTOR_TO_START;
        }

        if ($status === OrderStatus::STARTED) {
            return $this->contractorIs($order, $this->team)
                ? OrderStatus::READY_FOR_REVIEW
                : OrderStatus::ONGOING;
        }

        if ($status === OrderStatus::READY_FOR_REVIEW) {
            return $this->contractorIs($order, $this->team)
                ? OrderStatus::WAITING_FOR_CLIENT_REVIEW
                : OrderStatus::FINISHED;
        }

        if ($status === OrderStatus::REJECT) {
            return $this->contractorIs($order, $this->team)
                ? OrderStatus::WORK_ON_REVISIONS
                : OrderStatus::WAITING_FOR_CONTRACTOR_REVISION;
        }

        if ($status === OrderStatus::WORK_ON_REVISIONS) {
            return $this->contractorIs($order, $this->team)
                ? OrderStatus::READY_FOR_REVIEW
                : OrderStatus::WAITING_FOR_CONTRACTOR_REVISION;
        }

        if ($status === OrderStatus::FINISHED) {
            return OrderStatus::FULFILLED;
        }

        return $response;
    }

    protected function authoredByCompany(Company $company): bool
    {
        return filled($this->order->team_id) && $this->order->team_id === $company->getKey();
    }

    protected function authoredByUser(User $user): bool
    {
        return $this->order->loadMissing('userAuthor')->userAuthor->is($user) && blank($this->order->team_id);
    }
}
