<?php

declare(strict_types=1);

namespace App\Http\Controllers\FinalResults;

use App\Data\FinalResults\FinalStudentResultData;
use App\Data\Results\TranscriptData;
use App\Data\Students\StudentBasicData;
use App\Enums\StudentStatus;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\Student;
use App\Services\Pdf\PdfDocument;
use App\ViewModels\finalResults\FinalResultsIndexPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

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

        return redirect()->route('finalResults.index', ['student' => $student]);
    }

    public function print(Student $student): View
    {
        return view('pdfs.finalResults.view', [
            'results' => FinalStudentResultData::from($student),
            'student' => StudentBasicData::from($student),
        ]);
    }

    public function transcript(Student $student): View
    {
        $studentData = StudentBasicData::from($student);

        return view('pdfs.finalResults.transcript', [
            'results' => FinalStudentResultData::from($student),
            'student' => $studentData,
            'transcript' => TranscriptData::from($student->allowEGrade()),
        ]);
    }

    public function download(Student $student): HttpResponse
    {
        $studentData = StudentBasicData::from($student);

        return PdfDocument::view('pdfs.finalResults.transcript', [
            'results' => FinalStudentResultData::from($student),
            'student' => $studentData,
            'transcript' => TranscriptData::from($student->allowEGrade()),
        ])->download("{$studentData->registrationNumber}-results.pdf");
    }
}
