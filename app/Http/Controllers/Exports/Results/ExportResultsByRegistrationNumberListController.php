<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Exports\ResultsExport;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ExportResultsByRegistrationNumberListController
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['registration_numbers' => ['required', 'string']]);

        return redirect()->back()->success('Result export for Registration Number List started...');
    }

    public function download(Request $request): Response|BinaryFileResponse
    {
        $inputString = $request->string('registration_numbers')->value();

        $registrationNumbers = Str::of($inputString)
            ->replace(' ', '')
            ->explode(',')
            ->filter()
            ->unique();

        $uuid = Str::of(Str::ulid()->toString())->reverse()->limit(10, '')->reverse()->value();

        $studentIds = Student::query()
            ->whereIn('registration_number', $registrationNumbers)
            ->pluck('id');

        return ResultsExport::forStudents($studentIds->toArray())->download("students-{$uuid}-results.xlsx");
    }
}
