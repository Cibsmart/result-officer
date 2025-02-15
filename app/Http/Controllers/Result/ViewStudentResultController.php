<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentBasicData;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\Student;
use App\ViewModels\Results\ResultViewPage;
use Illuminate\Contracts\View\View;
use Inertia\Inertia;
use Inertia\Response;

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

    public function view(ExistingRegistrationNumberRequest $request): Response
    {
        $student = $request->input('student');

        return Inertia::render('results/view/page', new ResultViewPage(
            student: StudentBasicData::from($student),
            results: StudentResultData::from($student),
        ));
    }
}
