<?php

namespace Modules\Offer\Controllers;

use Illuminate\Http\Request;
use Modules\Offer\Actions\GenerateOfferPdf;
use Modules\Offer\Http\Controllers\Controller;
use Modules\Offer\Models\Offer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OfferPdfController extends Controller
{
    public function stream(Request $request, $offer): BinaryFileResponse
    {
        $offer = Offer::findByIdOrFail($offer);

        $pdfPath = GenerateOfferPdf::run($offer);

        return response()->file($pdfPath);
    }

    public function download(Request $request, $offer): BinaryFileResponse
    {
        $offer = Offer::findByIdOrFail($offer);

        $pdfPath = GenerateOfferPdf::run($offer);

        return response()->download($pdfPath, $offer->internal_id . '.pdf');
    }
}
