<?php

namespace Modules\Invoicify\Support\Pdf;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Traits\Macroable;
use Mpdf\Config\ConfigVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MpdfWrapper
{
    use Macroable;

    protected $mpdf;

    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;

        $defaultFontDirs = (new ConfigVariables)->getDefaults()['fontDir'];

        $tempDir = $this->getConfig('temp_dir');
        $cacheDir = $this->getConfig('cache_dir');
        $fontDir = $this->getConfig('font_dir');

        // Ensure directories exist and are writable before creating Mpdf instance
        // mPDF creates a subdirectory 'mpdf' within the temp directory
        File::ensureDirectoryExists($tempDir . '/mpdf');
        File::ensureDirectoryExists($cacheDir);
        File::ensureDirectoryExists($fontDir);

        $mpdfConfig = [
            'mode' => $this->getConfig('mode'),
            'format' => $this->getConfig('format'),
            'debug' => $this->getConfig('debug'),
            'allow_output_buffering' => $this->getConfig('allow_output_buffering'),
            'default_font' => $this->getConfig('default_font'),
            'simpleTables' => $this->getConfig('simpleTables'),
            'tempDir' => $tempDir,
            'cacheDir' => $cacheDir,

            'fontDir' => array_merge(
                $defaultFontDirs,
                [$fontDir],
            ),
        ];

        $this->mpdf = new Mpdf(array_merge($mpdfConfig, $this->config));

        $this->mpdf->SetTitle($this->getConfig('title'));
        $this->mpdf->SetAuthor($this->getConfig('author'));
        $this->mpdf->SetSubject($this->getConfig('subject'));
        $this->mpdf->SetKeywords($this->getConfig('keywords'));
        $this->mpdf->SetCreator($this->getConfig('creator'));

        $this->mpdf->dpi = $this->getConfig('dpi');
        $this->mpdf->PDFA = $this->getConfig('pdfa') ?: false;
        $this->mpdf->PDFAauto = $this->getConfig('pdfaauto') ?: false;
        $this->mpdf->PDFAversion = $this->getConfig('pdfaversion') ?: '3-B';
    }

    protected function getConfig($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : Config::get('invoicify.mpdf.' . $key);
    }

    public function getMpdf()
    {
        return $this->mpdf;
    }

    public function output($filename = '', $destination = Destination::STRING_RETURN)
    {
        if ($this->mpdf->allow_output_buffering) {
            try {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
            } catch (\Throwable $e) {
                // Silently ignore buffer cleanup errors
            }
        }

        return $this->mpdf->Output($filename, $destination);
    }

    public function save($filename)
    {
        File::ensureDirectoryExists(dirname($filename));

        return $this->output($filename, Destination::FILE);
    }

    public function download($filename = 'invoice.pdf')
    {
        return $this->output($filename, Destination::DOWNLOAD);
    }

    public function stream($filename = 'invoice.pdf')
    {
        return $this->output($filename, Destination::INLINE);
    }
}
