<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students;

use App\Data\Enums\CreditUnitListData;
use App\Data\Enums\StudentStatusListData;
use App\Data\Students\StudentBasicData;
use App\Data\Students\StudentComprehensiveData;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\Student;
use App\ViewModels\Students\StudentIndexPage;
use App\ViewModels\Students\StudentShowPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Inertia\Inertia;
use Inertia\Response;

final class StudentController
{
    public function index(): Response
    {
        $students = Student::query()
            ->with('program.department.faculty', 'entrySession', 'lga.state.country')
            ->paginate();

        $paginated = StudentBasicData::collect($students);
        assert($paginated instanceof AbstractPaginator);

        return Inertia::render('students/index/page', new StudentIndexPage(paginated: $paginated));
    }

    public function show(Request $request, ?Student $student = null): Response
    {
        $selectedIndex = $request->integer('selectedIndex');

        return Inertia::render('students/show/page', new StudentShowPage(
            data: fn () => $student ? StudentComprehensiveData::fromModel($student) : null,
            statues: StudentStatusListData::new(),
            units: CreditUnitListData::new(),
            selectedIndex: $selectedIndex,
        ));
    }

    public function store(ExistingRegistrationNumberRequest $request): RedirectResponse
    {
        $student = $request->input('student');

        return redirect()->to(route('students.show', ['student' => $student]));
    }
}
