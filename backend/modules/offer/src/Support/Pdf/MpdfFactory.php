<?php

namespace Modules\Offer\Support\Pdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class MpdfFactory
{
    public function getPdf(array $config = []): MpdfWrapper
    {
        return new MpdfWrapper($config);
    }

    public function loadHTML(string $html, array $config = []): MpdfWrapper
    {
        $pdf = $this->getPdf($config);
        $pdf->getMpdf()->WriteHTML($html);

        return $pdf;
    }

    public function loadFile(string $file, array $config = []): MpdfWrapper
    {
        return $this->loadHTML(File::get($file), $config);
    }

    public function loadView(string $view, array $data = [], array $mergeData = [], array $config = []): MpdfWrapper
    {
        return $this->loadHTML(View::make($view, $data, $mergeData)->render(), $config);
    }
}
