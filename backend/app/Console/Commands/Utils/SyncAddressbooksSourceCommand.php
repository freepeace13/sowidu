<?php

namespace App\Console\Commands\Utils;

use App\Models\Addressbook;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncAddressbooksSourceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-addressbooks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will sync addressbooks and add values on the `model_id` and `model_type` columns';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Addressbook::query()
            ->foreign()
            ->select(['id', 'email', 'model_id', 'model_type', 'foreign_type', 'name'])
            ->chunk(20, function (\Illuminate\Support\Collection $addressbooks) {
                $addressbooks->each(function (Addressbook $addressbook) {
                    $email = $addressbook->email;

                    if (!$addressbook->isForeign() || blank($email)) {
                        return;
                    }

                    $source = User::where('email', $email)->first();

                    if (!$source) {
                        return;
                    }

                    // Foreign person
                    if ($addressbook->isForeignPerson()) {
                        $addressbook->update([
                            'model_id' => $source->getKey(),
                            'model_type' => $source->getMorphClass(),
                        ]);

                        return;
                    }

                    // Foreign organization
                    if ($addressbook->isForeignOrganization()) {
                        $company = $this->findCompanyByOwner($source, $addressbook->name);

                        if (!$company) {
                            return;
                        }

                        $addressbook->update([
                            'model_id' => $company->getKey(),
                            'model_type' => $company->getMorphClass(),
                        ]);
                    }
                });
            });

        return Command::SUCCESS;
    }

    protected function findCompanyByOwner(User $owner, string $companyName)
    {
        // Get all companies owned by the user
        $companies = Company::where('user_id', $owner->getKey())->get();

        if ($companies->count() === 1) {
            return $companies->first();
        }

        // Search for a company with the same name
        $company = $companies->first(fn (Company $company) => Str::contains($company->name, $companyName));

        if (!$company) {
            return null;
        }

        return $company;
    }
}
