<?php

namespace App\Console\Commands\Company;

use App\Events\Organization\OrganizationInvoiceSettingsUpdated;
use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class TriggerCompanyInvoiceSettingsUpdatedCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:invoice-settings-updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will trigger the OrganizationInvoiceSettingsUpdated event for all companies that have invoice_defaults set.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return \Symfony\Component\Console\Command\Command::FAILURE;
        }

        $this->info('Saving payment date on invoices...');

        // Get all invoices where payment_date is null
        $companies = Company::query()
            ->whereJsonContainsKey('settings->invoice_defaults')
            ->get();

        $this->withProgressBar($companies, function (Company $company) {
            event(new OrganizationInvoiceSettingsUpdated($company));
        });

        return \Symfony\Component\Console\Command\Command::SUCCESS;
    }
}
