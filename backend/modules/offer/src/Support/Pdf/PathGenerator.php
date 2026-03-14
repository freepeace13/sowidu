<?php

namespace Modules\Offer\Support\Pdf;

use Modules\Offer\Models\Offer;

interface PathGenerator
{
    public function getPath(Offer $offer): string;

    public function getTempPath(Offer $offer): string;
}
