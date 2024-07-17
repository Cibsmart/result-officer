<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentData;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\View\View;

final class PrintStudentResultController extends Controller
{
    public function __invoke(Student $student): View
    {
        return view('pdfs.results.view', [
            'results' => StudentResultData::from($student),
            'student' => StudentData::from($student),
        ]);
    }
}
