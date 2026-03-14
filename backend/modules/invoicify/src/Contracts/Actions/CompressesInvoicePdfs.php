<?php

namespace Modules\Invoicify\Contracts\Actions;

use App\Models\User;

interface CompressesInvoicePdfs
{
    /**
     * Compress multiple invoice PDFs into a zip file.
     *
     * @param  User  $user  The user requesting the compression
     * @param  array  $invoiceIds  Array of invoice IDs to compress
     * @param  mixed  $teamId  Optional team ID for authorization context
     * @param  mixed  $errorBag  Optional error bag for validation errors
     * @return array{file_url: string, file_name: string} Array containing the file URL and filename
     */
    public function compress(User $user, array $invoiceIds, $teamId = null, $errorBag = ''): array;
}
