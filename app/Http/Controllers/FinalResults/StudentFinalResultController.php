<?php

declare(strict_types=1);

namespace App\Http\Controllers\FinalResults;

use App\Data\FinalResults\FinalStudentResultData;
use App\Data\Results\TranscriptData;
use App\Data\Students\StudentBasicData;
use App\Enums\StudentStatus;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\Student;
use App\ViewModels\finalResults\FinalResultsIndexPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

final class StudentFinalResultController
{
    public function index(?Student $student = null): Response
    {
        $cleared = $student
            ? in_array($student->status, StudentStatus::archivedStates(), true)
            : null;

        return Inertia::render('finalResults/index/page', new FinalResultsIndexPage(
            student: fn () => $student ? StudentBasicData::from($student) : null,
            results: fn () => $cleared ? FinalStudentResultData::from($student) : null,
        ));
    }

    public function store(ExistingRegistrationNumberRequest $request): RedirectResponse
    {
        $student = $request->input('student');

        return redirect()->to(route('finalResults.index', ['student' => $student]));
    }

    public function print(Student $student): View
    {
        return view('pdfs.finalResults.view', [
            'results' => FinalStudentResultData::from($student),
            'student' => StudentBasicData::from($student),
        ]);
    }

    public function transcript(Student $student): Pdf|PdfBuilder|View
    {
        $studentData = StudentBasicData::from($student);

        return Pdf::view('pdfs.finalResults.transcript', [
            'results' => FinalStudentResultData::from($student),
            'student' => $studentData,
            'transcript' => TranscriptData::from($student->allowEGrade()),
        ])
            ->withBrowsershot(static function (Browsershot $browsershot): void {
                $tempPath = Config::string('rp.chromium.temp');
                $browsershot->setChromePath(Config::string('rp.chromium.path'))
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
}
