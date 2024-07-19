<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Results\TranscriptData;
use App\Data\Students\StudentData;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

final class DownloadStudentTranscriptController extends Controller
{
    public function __invoke(Student $student): Pdf|PdfBuilder|View
    {
        $studentData = StudentData::from($student);

        return Pdf::view('pdfs.results.transcript', [
            'results' => StudentResultData::from($student),
            'student' => $studentData,
            'transcript' => TranscriptData::from($student->applyEGrade()),
        ])
            ->withBrowsershot(static function (Browsershot $browsershot): void {
                $browsershot->setChromePath(Config::string('rp-pdf.chromium.path'));
                $browsershot->scale(0.90);
            })
            ->format(Format::A4)
            ->margins(5, 5, 5, 5)
            ->name("$studentData->matriculationNumber-results.pdf");
    }
}
