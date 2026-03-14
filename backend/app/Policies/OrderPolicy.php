<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\Order;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Services\Order\OrderService;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\WorkLogs\Models\WorkLog;

class OrderPolicy
{
    use HandlesAuthorization, HandlesTeamAuthorization, InteractsWithImpersonator;

    protected $teamId;

    public function __construct()
    {
        $this->teamId = $this->getCurrentTeamId();
    }

    public function create(User $user)
    {
        if ($this->authIsPrivateUser()) {
            return true;
        }

        return $this->isAuthorizedTo($user, Permissions::CAN_CREATE_ORDER);
    }

    public function acceptOrConfirm(User $user, Order $order)
    {
        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_CONFIRM_ORDER,
            )
                && $this->ownedByAccount($order);
        }

        return $this->ownedByAccount($order); // Private account
    }

    public function cancelOrReject(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return $this->canParticipateOnOrder($user, $order)
                && $this->ownedByAccount($order);
        }

        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_CANCEL_ORDER,
            )
                && $this->ownedByAccount($order);
        }

        return $this->ownedByAccount($order); // Private account
    }

    public function delete(User $user, Order $order)
    {
        if (!$this->ownedByAccount($order)) {
            return Response::deny('This order falls outside of your account.');
        }

        if ($this->isImpersonating()) {
            return $this->canRepresentTeam(
                $user,
                $this->teamId,
                Permissions::CAN_CANCEL_ORDER,
            );
        }
    }

    public function update(User $user, Order $order)
    {
        return $this->ownedByAccount($order) && $this->isAuthorizedTo($user, Permissions::CAN_UPDATE_ORDER);
    }

    protected function ownedByAccount(Order $order): bool
    {
        return OrderService::make($this->getCurrentUser(), $this->getCurrentTeam())
            ->isContractor($order);
    }

    public function shareWorkLog(User $user, Order $order, WorkLog $workLog)
    {
        // Check if user can participate on this order
        if (
            OrderService::make($user, $this->getCurrentCompany())->isInvolvedOnOrder($order)
            && $order->contractor->isNot($this->getCurrentCompany())
        ) {
            return false;
        }

        // Check if user can toggle sharing on this `work-log`
        return $workLog->causer->is($user);
    }

    public function addManualTimeLog(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && OrderService::make($user, $this->getCurrentCompany())
                ->canStartTimeLog($order)
            && $this->isAuthorizedTo($user, Permissions::CAN_ADD_MANUAL_WORK_LOG_ENTRY);
    }

    public function viewOthersWorkLogs(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && $this->getCurrentCompany()
                ->isEmployed($user)
            && $this->isAuthorizedTo($user, Permissions::CAN_VIEW_OTHERS_WORK_LOGS);
    }

    public function manageProducts(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_ORDER_PRODUCTS)
            && $order->isOwnedByCompany($this->getCurrentCompany());
    }

    protected function canParticipateOnOrder(User $user, Order $order)
    {
        return OrderService::make($user, $this->getCurrentCompany())->isInvolvedOnOrder($order);
    }

    public function canChangeWorkLogOrder(User $user, Order $order, WorkLog $workLog)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        // Check if user is the owner of the work_log
        return $workLog->causer->is($user) || $this->isAuthorizedTo($user, Permissions::CAN_CHANGE_WORK_LOGS_ORDER) && $this->ownedByAccount($order);
    }

    public function manageDeliveryTickets(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_ORDER_DELIVERY_TICKETS)
            && ($order->isOwnedByCompany($this->getCurrentCompany()) || $order->isContractor($this->getCurrentCompany()));
    }

    public function accessDeliveryTickets(User $user, Order $order)
    {

        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && $this->isAuthorizedTo($user, Permissions::CAN_ACCESS_DELIVERY_TICKETS)
            && ($order->isOwnedByCompany($this->getCurrentCompany()) || $order->isContractor($this->getCurrentCompany()));
    }

    public function manageOrderInvoices(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_INVOICES)
            && $order->isOwnedByCompany($this->getCurrentCompany());
    }

    public function accessInvoices(User $user, Order $order)
    {
        if ($this->authIsPrivateUser()) {
            return false;
        }

        return $this->canParticipateOnOrder($user, $order)
            && $this->isAuthorizedTo($user, Permissions::CAN_ACCESS_INVOICES);
    }
}
