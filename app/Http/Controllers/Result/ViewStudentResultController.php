<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Results\TranscriptData;
use App\Data\Students\StudentData;
use App\Http\Requests\Results\ResultRequest;
use App\Models\Student;
use App\ViewModels\Results\ResultViewPage;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

final readonly class ViewStudentResultController
{
    public function form(): Response
    {
        return Inertia::render('results/form/page');
    }

    public function print(Student $student): View
    {
        return view('pdfs.results.view', [
            'results' => StudentResultData::from($student),
            'student' => StudentData::from($student),
        ]);
    }

    public function transcript(Student $student): Pdf|PdfBuilder|View
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
            ->name("$studentData->registrationNumber-results.pdf");
    }

    public function view(ResultRequest $request): Response
    {
        $registrationNumber = $request->input('registration_number');

        $studentModel = Student::query()
            ->where('registration_number', $registrationNumber)
            ->firstOrFail();

        return Inertia::render('results/view/page', new ResultViewPage(
            student: StudentData::from($studentModel),
            results: StudentResultData::from($studentModel),
        ));
    }
}
