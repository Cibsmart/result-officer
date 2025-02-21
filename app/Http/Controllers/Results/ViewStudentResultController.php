<?php

declare(strict_types=1);

namespace App\Http\Controllers\Results;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentBasicData;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\Student;
use App\ViewModels\Results\ResultViewPage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ViewStudentResultController
{
    public function index(?Student $student = null): Response
    {
        return Inertia::render('results/index/page', new ResultViewPage(
            student: fn () => $student ? StudentBasicData::from($student) : null,
            results: fn () => $student ? StudentResultData::from($student) : null,
        ));
    }

    public function store(ExistingRegistrationNumberRequest $request): RedirectResponse
    {
        $student = $request->input('student');

        return redirect()->route('results.index', ['student' => $student]);
    }

    public function print(Student $student): View
    {
        return view('pdfs.results.view', [
            'results' => StudentResultData::from($student),
            'student' => StudentBasicData::from($student),
        ]);
    }
}
