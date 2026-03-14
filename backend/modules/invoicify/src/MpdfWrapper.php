<?php

namespace Modules\Invoicify;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class MpdfWrapper
{
    protected Mpdf $mpdf;

    protected $htmlLoaded = false;

    public function __construct(array $config = [])
    {
        $this->mpdf = new Mpdf(array_merge([
            'mode' => 's',
            'format' => 'A4',
            'debug' => true,
            'allow_output_buffering' => true,
            'default_font' => 'dejavusans',
        ], $config));
    }

    public function component($component): static
    {
        return $this->loadHtml(
            Blade::renderComponent($component),
        );
    }

    public function loadHtml(string $html): static
    {
        if (!$this->htmlLoaded) {
            $this->mpdf->WriteHTML($html);

            $this->htmlLoaded = true;
        }

        return $this;
    }

    public function output()
    {
        return $this->mpdf->Output();
    }

    /** @deprecated */
    public function template(string $path, array $data = []): static
    {
        $template = '<x-{{ component }} {{ bindings }} />';

        // Process image URLs before rendering
        $data = $this->processImageUrls($data);

        $component = str_replace(
            [
                '{{ component }}',
                '{{ bindings }}',
            ],
            [
                Str::start($path, 'pdfcraft-template::'),
                $this->compileBindings($data),
            ],
            $template,
        );

        $html = Blade::render($component, $data, deleteCachedView: true);

        $this->loadHtml($html);

        return $this;
    }

    protected function processImageUrls(array $data): array
    {
        array_walk_recursive($data, function (&$value) {
            if (is_string($value) && $this->isImageUrl($value)) {
                $value = $this->getProcessedImageUrl($value);
            }
        });

        return $data;
    }

    protected function isImageUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $url);
    }

    protected function getProcessedImageUrl(string $url): string
    {
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                // Convert to base64
                $base64 = base64_encode($response);
                $mime = $this->getMimeType($url);

                return "data:{$mime};base64,{$base64}";
            }
        } catch (\Exception $e) {
            Log::warning("Failed to process image URL: {$url}", [
                'error' => $e->getMessage(),
            ]);
        }

        // Return a default image or empty string if processing fails
        return '';
    }

    protected function getMimeType(string $url): string
    {
        $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    protected function compileBindings($data): string
    {
        return collect(array_keys($data))
            ->map(fn ($key) => ":\${$key}")
            ->implode(' ');
    }

    // public function loadView(string $view, array $data = []): static
    // {
    //     $html = view($view)->with($data)->render();

    //     $this->loadHtml($html);

    //     return $this;
    // }
}
