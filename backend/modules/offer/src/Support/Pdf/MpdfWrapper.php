<?php

namespace Modules\Offer\Support\Pdf;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Traits\Macroable;
use Mpdf\Config\ConfigVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MpdfWrapper
{
    use Macroable;

    protected Mpdf $mpdf;

    protected array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;

        $defaultFontDirs = (new ConfigVariables)->getDefaults()['fontDir'];

        $mpdfConfig = [
            'mode' => $this->getConfig('mode'),
            'format' => $this->getConfig('format'),
            'debug' => $this->getConfig('debug'),
            'allow_output_buffering' => $this->getConfig('allow_output_buffering'),
            'default_font' => $this->getConfig('default_font'),
            'simpleTables' => $this->getConfig('simpleTables'),
            'tempDir' => $this->getConfig('temp_dir'),
            'cacheDir' => $this->getConfig('cache_dir'),

            'fontDir' => array_merge(
                $defaultFontDirs,
                [$this->getConfig('font_dir')],
            ),

            // Enable remote content (images, etc.)
            'curlFollowLocation' => true,
            'curlAllowUnsafeSslRequests' => true,
        ];

        $this->mpdf = new Mpdf(array_merge($mpdfConfig, $this->config));

        $this->mpdf->SetTitle($this->getConfig('title') ?? '');
        $this->mpdf->SetAuthor($this->getConfig('author') ?? '');
        $this->mpdf->SetSubject($this->getConfig('subject') ?? '');
        $this->mpdf->SetKeywords($this->getConfig('keywords') ?? '');
        $this->mpdf->SetCreator($this->getConfig('creator') ?? '');

        $this->mpdf->dpi = $this->getConfig('dpi');
        $this->mpdf->PDFA = $this->getConfig('pdfa') ?: false;
        $this->mpdf->PDFAauto = $this->getConfig('pdfaauto') ?: false;
        $this->mpdf->PDFAversion = $this->getConfig('pdfaversion') ?: '3-B';
    }

    protected function getConfig(string $key): mixed
    {
        return $this->config[$key] ?? Config::get('offer.mpdf.' . $key);
    }

    public function getMpdf(): Mpdf
    {
        return $this->mpdf;
    }

    public function output(string $filename = '', string $destination = Destination::STRING_RETURN): ?string
    {
        return $this->mpdf->Output($filename, $destination);
    }

    /**
     * Get the PDF content as a string.
     */
    public function content(): string
    {
        return $this->output('', Destination::STRING_RETURN);
    }

    public function save(string $filename): ?string
    {
        File::ensureDirectoryExists(dirname($filename));

        return $this->output($filename, Destination::FILE);
    }

    /**
     * Get a download response for the PDF.
     */
    public function download(string $filename = 'offer.pdf'): \Illuminate\Http\Response
    {
        return new \Illuminate\Http\Response($this->content(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get an inline stream response for the PDF.
     */
    public function stream(string $filename = 'offer.pdf'): \Illuminate\Http\Response
    {
        return new \Illuminate\Http\Response($this->content(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
