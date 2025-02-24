<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students;

use App\Actions\Students\StudentDeleteAction;
use App\Data\Enums\StudentStatusListData;
use App\Data\Students\StudentBasicData;
use App\Data\Students\StudentComprehensiveData;
use App\Enums\NotificationType;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use App\ViewModels\Students\StudentIndexPage;
use App\ViewModels\Students\StudentShowPage;
use Exception;
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
            statues: Inertia::optional(fn () => StudentStatusListData::new()),
            selectedIndex: $selectedIndex,
        ));
    }

    public function store(ExistingRegistrationNumberRequest $request): RedirectResponse
    {
        $student = $request->input('student');

        return redirect()->to(route('students.show', ['student' => $student]));
    }

    public function destroy(
        Student $student,
        StudentDeleteRequest $request,
        StudentDeleteAction $action,
    ): RedirectResponse {
        $validated = $request->validated();

        $user = $request->user();
        assert($user instanceof User);

        $dbMail = null;

        if ($request->validated('has_mail')) {
            $dbMail = DBMail::createFromRecordUpdate(
                user: $user,
                mailTitle: $validated['mail_title'],
                mailDate: DateValue::fromValue($validated['mail_date']),
            );
        }

        try {
            $action->execute(student: $student, remark: $validated['remark'], dbMail: $dbMail, user: $user);
        } catch (Exception $e) {
            return redirect()
                ->route('students.show', ['student' => $student])
                ->{NotificationType::ERROR->value}("{$e->getMessage()}");
        }

        return redirect()
            ->route('students.index')
            ->{NotificationType::SUCCESS->value}("Student ({$student->registration_number}) deleted successfully.");
    }
}
