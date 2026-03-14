<?php

namespace Modules\Offer\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Offer\Support\Pdf\MpdfFactory;
use Modules\Offer\Support\Pdf\MpdfWrapper;

/**
 * @method static MpdfWrapper loadHTML(string $html, array $config = [])
 * @method static MpdfWrapper loadFile(string $file, array $config = [])
 * @method static MpdfWrapper loadView(string $view, array $data = [], array $mergeData = [], array $config = [])
 *
 * @see MpdfFactory
 */
class Pdf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'offer.mpdf';
    }
}
