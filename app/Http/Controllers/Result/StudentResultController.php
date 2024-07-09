<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Results\ResultRequest;
use App\Models\Student;
use App\Pages\Results\ResultViewPage;
use Inertia\Inertia;
use Inertia\Response;

final class StudentResultController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('results/form/page');
    }

    public function view(ResultRequest $request): Response
    {
        $matriculationNumber = $request->input('matriculation_number');

        $studentModel = Student::query()->where('matriculation_number', $matriculationNumber)->firstOrFail();

        return Inertia::render('results/view/page', new ResultViewPage(
            student: StudentData::from($studentModel),
            results: StudentResultData::from($studentModel),
        ));
    }
}
