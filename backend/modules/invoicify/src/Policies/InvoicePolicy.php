<?php

namespace Modules\Invoicify\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Invoicify\Contracts\External\CompanyServiceContract;
use Modules\Invoicify\Models\Invoice;
use Modules\Shared\Enums\Permissions;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function __construct(
        protected CompanyServiceContract $companyService,
    ) {}

    public function view(User $user, Invoice $invoice, ?int $teamId = null)
    {
        // check authorization for team invoices
        if ($teamId) {
            $currentTeam = $this->companyService->getCompanyById($teamId);
            if (!$currentTeam) {
                return false;
            }

            $membership = $user->teamMembership($currentTeam);
            if (!$membership) {
                return false;
            }

            if (!$membership->can(Permissions::CAN_MANAGE_INVOICES)) {
                return false;
            }

            if ($invoice->company_id !== $currentTeam->getKey()) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, Invoice $invoice, ?int $teamId = null)
    {
        if ($teamId) {
            $currentTeam = $this->companyService->getCompanyById($teamId);
            if (!$currentTeam) {
                return false;
            }

            $membership = $user->teamMembership($currentTeam);
            if (!$membership) {
                return false;
            }

            if (!$membership->can(Permissions::CAN_MANAGE_INVOICES)) {
                return false;
            }

            if ($invoice->company_id !== $currentTeam->getKey()) {
                return false;
            }
        }

        if ($invoice->user_id !== $user->getKey()) {
            return false;
        }
    }
}
