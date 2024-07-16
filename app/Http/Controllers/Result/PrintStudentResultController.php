<?php

declare(strict_types=1);

namespace App\Http\Controllers\Result;

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentData;
use App\Models\Student;
use Illuminate\View\View;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

final class PrintStudentResultController
{
    public function __invoke(Student $student): Pdf|PdfBuilder|View
    {
        return view('pdfs.results.view', [
            'results' => StudentResultData::from($student),
            'student' => StudentData::from($student),
        ]);
    }
}
