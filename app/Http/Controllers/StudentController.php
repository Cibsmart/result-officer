<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Students\StudentComprehensiveData;
use App\Data\Students\StudentData;
use App\Http\Requests\Results\ResultRequest;
use App\Models\Student;
use App\ViewModels\Students\StudentIndexPage;
use App\ViewModels\Students\StudentShowPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class StudentController
{
    public function index(): Response
    {
        $students = Student::query()
            ->with('program.department.faculty', 'entrySession', 'government.state.country')
            ->paginate();

        return Inertia::render('students/index/page', new StudentIndexPage(
            paginated: StudentData::collect($students),
        ));
    }

    public function show(Request $request, ?Student $student = null): Response
    {
        $selectedIndex = $request->integer('selectedIndex');

        return Inertia::render('students/show/page', new StudentShowPage(
            student: fn () => $student ? StudentComprehensiveData::fromModel($student) : null,
            selectedIndex: $selectedIndex,
        ));
    }

    public function store(ResultRequest $request): RedirectResponse
    {
        $student = Student::query()
            ->where('registration_number', $request->input('registration_number'))
            ->first();

        return redirect()->to(route('students.show', ['student' => $student]));
    }
}
