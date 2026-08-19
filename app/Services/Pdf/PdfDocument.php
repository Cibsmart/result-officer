<?php

declare(strict_types=1);

namespace App\Services\Pdf;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;

final class PdfDocument
{
    private const string FORMAT = 'A4';

    private const float MARGIN = 5.0;

    private function __construct(private readonly string $html)
    {
    }

    /** @param array<string, object> $data */
    public static function view(string $view, array $data = []): self
    {
        return new self(View::make($view, $data)->render());
    }

    public function content(): string
    {
        $tempDirectory = Config::string('rp.pdf.temp');

        File::ensureDirectoryExists($tempDirectory);

        $mpdf = new Mpdf([
            'format' => self::FORMAT,
            'margin_bottom' => self::MARGIN,
            'margin_left' => self::MARGIN,
            'margin_right' => self::MARGIN,
            'margin_top' => self::MARGIN,
            'tempDir' => $tempDirectory,
        ]);

        $mpdf->WriteHTML($this->html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    public function download(string $fileName): Response
    {
        return new Response($this->content(), Response::HTTP_OK, [
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Content-Type' => 'application/pdf',
        ]);
    }
}
