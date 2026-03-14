<?php

namespace Modules\Invoicify\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Invoicify\Support\Pdf\MpdfWrapper as Mpdf;
use Modules\Invoicify\Support\Pdf\ViewComponent;

/**
 * Class MpdfFactory
 *
 * @method static Mpdf loadHTML(string $html, ?array $config = [])
 * @method static Mpdf loadFile(string $file, ?array $config = [])
 * @method static Mpdf loadView(string $view, ?array $data = [], ?array $mergeData = [], ?array $config = [])
 * @method static Mpdf loadComponent(ViewComponent $component, ?array $config = [])
 */
class Pdf extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'invoicify.mpdf';
    }
}
