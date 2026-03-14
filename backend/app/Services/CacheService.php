<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public static function companyCurrencyKey(int $company): string
    {
        return "company.{$company}.currency";
    }

    public static function getCompanyCurrency(int|Company $company): string
    {
        if ($company instanceof Company) {
            $company = $company->id;
        }

        return Cache::remember(
            static::companyCurrencyKey($company),
            now()->addDay(),
            function () use ($company) {
                return Company::findOrFail($company)->currency ?? config('app.default.currency');
            },
        );

    }

    public static function storeCompanyCurrency(int|Company $company, string $currency)
    {
        if ($company instanceof Company) {
            $company = $company->id;
        }

        return Cache::put(
            static::companyCurrencyKey($company),
            $currency,
            now()->addDay(),
        );
    }

    public static function hasUserRequestedBulkInvoiceExport(User $user): bool
    {
        return cache()->get("user.{$user->id}.bulk_invoice_export", false);
    }

    public static function userRequestedBulkInvoiceExport(
        User $user,
        bool $requested = true,
    ) {
        return cache()->put(
            "user.{$user->id}.bulk_invoice_export",
            $requested,
            now()->addMinutes(5),
        );
    }

    public static function userRequestedBulkInvoiceExportFinished(
        User $user,
    ) {
        return cache()->put(
            "user.{$user->id}.bulk_invoice_export",
            false,
            now()->addMinutes(5),
        );
    }
}
