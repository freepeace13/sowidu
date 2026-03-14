<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use App\Services\Order\Traits\CanIdentifyOrderParties;
use Illuminate\Support\Collection;

class OrderRepository
{
    use CanIdentifyOrderParties;

    protected Order $order;

    public function __construct(protected User $user, protected ?Company $company = null) {}

    public static function make(User $user, ?Company $company = null): static
    {
        return new static($user, $company);
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function onOrder(Order $order): self
    {
        $this->setOrder($order);

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getInvoicesWithPayments()
    {
        return $this->order->invoices()
            ->withPayments()
            ->get();
    }

    public function fetchDeductionInvoiceCandidates()
    {
        return $this->order->invoices()
            ->notDraft()
            ->doesntHave('deductible')
            ->get();
    }

    public function invoices(): Collection
    {
        $order = $this->getOrder();

        if ($this->isViewerIsClient()) {
            return $order->invoices()
                ->exceptDraft()
                ->outgoing() // Show outgoing invoices only
                ->with(['items'])
                ->get();
        }

        return $order->invoices()
            ->with(['items'])
            ->get();
    }

    public function isContractorOn(Order $order): bool
    {
        if (!$this->company) {
            return false;
        }

        return $order->contractorIs($this->company);
    }

    public function isViewerIsContactor(): bool
    {
        return $this->isContractorOn($this->order);
    }

    public function isViewerIsClient(): bool
    {
        return !$this->isContractorOn($this->order);
    }

    public function deliveryTickets(): Collection
    {
        $order = $this->getOrder();
        $deliverTickets = $order->deliveryTickets();
        if ($this->isViewerIsClient()) {
            return $deliverTickets
                ->outgoing() // Show outgoing invoices only
                ->with(['invoices'])
                ->get();
        }

        return $deliverTickets
            ->with(['invoices'])
            ->get();
    }
}
