<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Results\TranscriptData;
use App\Data\Students\StudentBasicData;
use App\Http\Requests\Results\ResultRequest;
use App\Models\Student;
use App\ViewModels\Results\ResultViewPage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
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
            'student' => StudentBasicData::from($student),
        ]);
    }

    public function transcript(Student $student): Pdf|PdfBuilder|View
    {
        $studentData = StudentBasicData::from($student);

        return Pdf::view('pdfs.results.transcript', [
            'results' => StudentResultData::from($student),
            'student' => $studentData,
            'transcript' => TranscriptData::from($student->allowEGrade()),
        ])
            ->withBrowsershot(static function (Browsershot $browsershot): void {
                $tempPath = Config::string('rp_pdf.chromium.temp');
                $browsershot->setChromePath(Config::string('rp_pdf.chromium.path'))
                    ->setOption('args', ['--disable-web-security'])
                    ->ignoreHttpsErrors()
                    ->noSandbox()
                    ->setCustomTempPath("{$tempPath}/browsershot-html")
                    ->addChromiumArguments([
                        'disk-cache-dir' => "{$tempPath}/user-data/Default/Cache",
                        'enable-font-antialiasing' => true,
                        'font-render-hinting' => 'none',
                        'force-device-scale-factor' => 1,
                        'hide-scrollbars' => true,
                        'lang' => 'en-US,en;q=0.9',
                        'user-data-dir' => "{$tempPath}/user-data",
                    ])
                    ->newHeadless()
                    ->scale(0.90);
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
            student: StudentBasicData::from($studentModel),
            results: StudentResultData::from($studentModel),
        ));
    }
}
