<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Policies\Traits\HandlesTeamAuthorization;
use App\Repositories\OrderRepository;
use App\Traits\InteractsWithImpersonator;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;
    use HandlesTeamAuthorization;
    use InteractsWithImpersonator;

    public function before(User $user, $ability)
    {
        if (!$this->isImpersonating()) {
            return false;
        }
    }

    public function view(User $user, Invoice $invoice, $teamId = null)
    {
        /** @todo
         *  1. check if user belongs to team
         *  2. check if user has permission to manage invoices
         *  3. check if team is either contractor or biller of invoice (when teamId is given)
         *  4. check if user is either contractor or biller of invoice (when teamId is not given)
         * */

        return true;
    }

    public function create(User $user)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_INVOICES);
    }

    public function update(User $user, Invoice $invoice)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_INVOICES)
            && $invoice->isOwnedByCompany($this->getCurrentCompany());
    }

    public function delete(User $user, Invoice $invoice)
    {
        // Check if user is the owner or has permissions
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_INVOICES)
            && $invoice->isOwnedByCompany($this->getCurrentCompany());
    }

    public function manageDocuments(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_MANAGE_INVOICES_DOCUMENTS,
        ) && $invoice->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }

    public function manageItems(User $user, Invoice $invoice, ?InvoiceItem $item = null)
    {
        if ($item) {
            if ($item->invoice->isNot($invoice)) {
                return false;
            }
        }

        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_MANAGE_INVOICES_ITEMS,
        ) && $invoice->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }

    public function sendToClient(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_SEND_INVOICES_TO_CLIENT,
        ) && $invoice->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }

    public function markAsPaid(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_MARK_INVOICES_AS_PAID,
        ) && $invoice->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }

    public function manageTaxes(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_TAXES)
            && $invoice->isOwnedByCompany(
                $this->getCurrentCompany(),
            );
    }

    public function viewPayments(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_VIEW_INVOICE_PAYMENTS)
            && $invoice->isOwnedByCompany(
                $this->getCurrentCompany(),
            ) || OrderRepository::make($user)
                ->onOrder($invoice->order)
                ->isViewerIsClient();
    }

    public function managePayments(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_INVOICE_PAYMENTS)
            && $invoice->isOwnedByCompany(
                $this->getCurrentCompany(),
            );
    }

    public function addPayments(User $user)
    {
        return $this->isAuthorizedTo($user, Permissions::CAN_MANAGE_INVOICE_PAYMENTS);
    }

    public function manageManualItems(User $user, Invoice $invoice)
    {
        return $this->isAuthorizedTo(
            $user,
            Permissions::CAN_MANAGE_INVOICE_MANUAL_ITEMS,
        ) && $invoice->isOwnedByCompany(
            $this->getCurrentCompany(),
        );
    }
}
