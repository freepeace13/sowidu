<?php

namespace Modules\Invoicify\Support\Pdf;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class MpdfFactory
{
    public function getPdf($config = [])
    {
        return new MpdfWrapper($config);
    }

    public function loadHTML($html, $config = [])
    {
        $pdf = $this->getPdf($config);
        $pdf->getMpdf()->WriteHTML($html);

        return $pdf;
    }

    public function loadFile($file, $config = [])
    {
        return $this->loadHTML(File::get($file), $config);
    }

    public function loadView($view, $data = [], $mergeData = [], $config = [])
    {
        return $this->loadHTML(View::make($view, $data, $mergeData)->render(), $config);
    }

    public function loadComponent($component, $config = [])
    {
        return $this->loadHTML(Blade::renderComponent($component), $config);
    }
}
