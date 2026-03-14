<?php

namespace App\Actions\Invoice;

use App\Actions\Traits\AsAction;
use App\Contracts\Actions\SavesInvoiceAsPdfs;
use App\Models\User;
use Mpdf\Mpdf;
use Packages\Invoice\Invoice;

class SaveInvoiceAsPdf implements SavesInvoiceAsPdfs
{
    use AsAction;

    /**
     * The output layout of the document depends on this settings.
     * Do not change unless you know how to.
     *
     * @see https://mpdf.github.io/headers-footers/headers-top-margins.html
     */
    const MARGIN_LEFT = 20; // mm

    const MARGIN_RIGHT = 10;
    const MARGIN_TOP = 90;
    const MARGIN_BOTTOM = 30;
    const MARGIN_HEADER = 45; // mm
    const MARGIN_FOOTER = 5;

    // A4 = 210mmx297mm

    public function saveAsPdf(Invoice $invoice, ?User $user, $teamId = null, $errorBag = null)
    {
        // check  user authorization if given and allowed to export the given invoice
        // check the employee authorization of the invoice if teamId and user are given
        // generate pdf file for invoice
        $pdf = new Mpdf([
            'debug' => true,
            'allow_output_buffering' => true,
            'margin_left' => self::MARGIN_LEFT,
            'margin_right' => self::MARGIN_RIGHT,
            'margin_top' => self::MARGIN_TOP,
            'margin_bottom' => self::MARGIN_BOTTOM,
            'margin_header' => self::MARGIN_HEADER,
            'margin_footer' => self::MARGIN_FOOTER,
        ]);

        $pdf->showImageErrors = true;

        $pdf->WriteHTML(view('invoice.pdf', [
            'invoice' => $invoice,
            // ...any context data
        ])->render());

        // return the pdf file
        return $pdf;
    }
}
