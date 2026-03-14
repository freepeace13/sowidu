<?php

namespace Modules\Offer\Support\Pdf;

use Modules\Offer\Models\Offer;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(Offer $offer): string
    {
        return storage_path("app/offers/{$offer->internal_id}.pdf");
    }

    public function getTempPath(Offer $offer): string
    {
        return storage_path("app/offers/temp/{$offer->internal_id}.pdf");
    }
}
