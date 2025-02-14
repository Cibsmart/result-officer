<?php

declare(strict_types=1);

namespace App\Http\Controllers\FinalResults;

use App\Data\FinalResults\FinalStudentResultData;
use App\Data\Students\StudentBasicData;
use App\Enums\StudentStatus;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\Student;
use App\ViewModels\finalResults\FinalResultsIndexPage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

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
        $student = Student::query()
            ->where('registration_number', $request->input('registration_number'))
            ->first();

        return redirect()->to(route('finalResults.index', ['student' => $student]));
    }
}
